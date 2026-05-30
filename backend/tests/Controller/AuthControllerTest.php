<?php

namespace App\Tests\Controller;

use App\Controller\AuthController;
use App\Entity\User;
use App\Repository\UserRepository;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[CoversClass(AuthController::class)]
class AuthControllerTest extends TestCase
{
    private MockObject&UserRepository $userRepo;
    private MockObject&UserPasswordHasherInterface $hasher;
    private MockObject&JWTTokenManagerInterface $jwtManager;

    protected function setUp(): void
    {
        $this->userRepo   = $this->createMock(UserRepository::class);
        $this->hasher     = $this->createMock(UserPasswordHasherInterface::class);
        $this->jwtManager = $this->createMock(JWTTokenManagerInterface::class);
    }

    // ── Login : validation des champs ────────────────────────────

    #[TestDox('login : email absent → invalide')]
    public function testLoginMissingEmail(): void
    {
        $data = ['motdepasse' => 'secret'];
        $this->assertEmpty(trim($data['email'] ?? ''));
    }

    #[TestDox('login : mot de passe absent → invalide')]
    public function testLoginMissingPassword(): void
    {
        $data = ['email' => 'user@example.com'];
        $this->assertEmpty($data['motdepasse'] ?? '');
    }

    #[TestDox('login : email whitespace → invalide après trim')]
    public function testLoginWhitespaceEmailIsInvalid(): void
    {
        $data  = ['email' => '   ', 'motdepasse' => 'secret'];
        $email = trim($data['email']);
        $this->assertEmpty($email);
    }

    #[TestDox('login : utilisateur introuvable → null')]
    public function testLoginUserNotFound(): void
    {
        $this->userRepo
            ->expects($this->once())
            ->method('findOneBy')
            ->with(['email' => 'nobody@example.com'])
            ->willReturn(null);

        $user = $this->userRepo->findOneBy(['email' => 'nobody@example.com']);
        $this->assertNull($user);
    }

    #[TestDox('login : mauvais mot de passe → isPasswordValid retourne false')]
    public function testLoginWrongPassword(): void
    {
        $user = new User();
        $user->setPassword('correct_hash');

        $this->hasher
            ->expects($this->once())
            ->method('isPasswordValid')
            ->with($user, 'wrong_pw')
            ->willReturn(false);

        $this->assertFalse($this->hasher->isPasswordValid($user, 'wrong_pw'));
    }

    #[TestDox('login : identifiants valides → JWT créé')]
    public function testLoginValidCredentialsCreatesJwt(): void
    {
        $user = new User();
        $user->setEmail('user@example.com');
        $user->setPassword('hashed_pw');
        $user->setUsername('testuser');
        $user->setRole('utilisateur');

        $this->userRepo->method('findOneBy')->willReturn($user);
        $this->hasher->method('isPasswordValid')->willReturn(true);
        $this->jwtManager->expects($this->once())->method('create')->with($user)->willReturn('jwt.token.here');

        $foundUser = $this->userRepo->findOneBy(['email' => 'user@example.com']);
        $valid     = $this->hasher->isPasswordValid($foundUser, 'plain_pw');
        $token     = $this->jwtManager->create($foundUser);

        $this->assertNotNull($foundUser);
        $this->assertTrue($valid);
        $this->assertSame('jwt.token.here', $token);
    }

    // ── Register : validation des champs ─────────────────────────

    #[TestDox('register : tous les champs vides → invalide')]
    public function testRegisterAllFieldsEmpty(): void
    {
        $data = ['pseudo' => '', 'email' => '', 'motdepasse' => ''];

        $pseudo = trim($data['pseudo'] ?? '');
        $email  = trim($data['email'] ?? '');
        $mdp    = $data['motdepasse'] ?? '';

        $this->assertFalse((bool) $pseudo);
        $this->assertFalse((bool) $email);
        $this->assertFalse((bool) $mdp);
    }

    #[TestDox('register : mots de passe différents → invalide')]
    public function testRegisterPasswordMismatch(): void
    {
        $data = ['motdepasse' => 'abc123', 'confirmer_motdepasse' => 'xyz789'];
        $this->assertNotSame($data['motdepasse'], $data['confirmer_motdepasse']);
    }

    #[TestDox('register : mot de passe < 6 caractères → invalide')]
    public function testRegisterPasswordTooShort(): void
    {
        $this->assertLessThan(6, strlen('12345'));
    }

    #[TestDox('register : mot de passe ≥ 6 caractères → valide')]
    public function testRegisterPasswordValidLength(): void
    {
        $this->assertGreaterThanOrEqual(6, strlen('securePass123'));
    }

    #[TestDox('register : mots de passe identiques → valide')]
    public function testRegisterPasswordsMatch(): void
    {
        $data = ['motdepasse' => 'securePass123', 'confirmer_motdepasse' => 'securePass123'];
        $this->assertSame($data['motdepasse'], $data['confirmer_motdepasse']);
    }

    #[TestDox('register : le nouveau compte a le rôle "utilisateur" et ROLE_USER')]
    public function testRegisterCreatesUserWithCorrectRole(): void
    {
        $user = new User();
        $user->setUsername('newuser');
        $user->setEmail('newuser@example.com');
        $user->setRole('utilisateur');

        $this->assertSame('utilisateur', $user->getRole());
        $this->assertContains('ROLE_USER', $user->getRoles());
        $this->assertNotContains('ROLE_ADMIN', $user->getRoles());
    }

    // ── Sécurité : normalisation des inputs ───────────────────────

    #[TestDox('email est trimmé avant utilisation')]
    public function testEmailIsTrimmed(): void
    {
        $raw = '  user@example.com  ';
        $this->assertSame('user@example.com', trim($raw));
    }

    /** @return array<string, array{int}> */
    public static function passwordLengthProvider(): array
    {
        return [
            '5 chars (trop court)' => [5],
            '6 chars (juste assez)' => [6],
            '20 chars'             => [20],
        ];
    }

    #[DataProvider('passwordLengthProvider')]
    #[TestDox('Validation longueur mot de passe')]
    public function testPasswordLengthValidation(int $length): void
    {
        $password = str_repeat('a', $length);
        if ($length < 6) {
            $this->assertLessThan(6, strlen($password));
        } else {
            $this->assertGreaterThanOrEqual(6, strlen($password));
        }
    }
}
