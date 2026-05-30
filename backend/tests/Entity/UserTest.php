<?php

namespace App\Tests\Entity;

use App\Entity\User;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

#[CoversClass(User::class)]
class UserTest extends TestCase
{
    private User $user;

    protected function setUp(): void
    {
        $this->user = new User();
    }

    #[TestDox('Le rôle par défaut est "utilisateur"')]
    public function testDefaultRole(): void
    {
        $user = new User();
        $this->assertSame('utilisateur', $user->getRole());
    }

    #[TestDox('createdAt est initialisé dans le constructeur')]
    public function testCreatedAtIsSetOnConstruct(): void
    {
        $user = new User();
        $this->assertInstanceOf(\DateTimeImmutable::class, $user->getCreatedAt());
    }

    #[TestDox('setUsername / getUsername')]
    public function testSetAndGetUsername(): void
    {
        $this->user->setUsername('testuser');
        $this->assertSame('testuser', $this->user->getUsername());
    }

    #[TestDox('setEmail / getEmail')]
    public function testSetAndGetEmail(): void
    {
        $this->user->setEmail('test@example.com');
        $this->assertSame('test@example.com', $this->user->getEmail());
    }

    #[TestDox('setPassword / getPassword')]
    public function testSetAndGetPassword(): void
    {
        $this->user->setPassword('hashed_password_here');
        $this->assertSame('hashed_password_here', $this->user->getPassword());
    }

    #[TestDox('setRole / getRole')]
    public function testSetAndGetRole(): void
    {
        $this->user->setRole('admin');
        $this->assertSame('admin', $this->user->getRole());
    }

    #[TestDox('getUserIdentifier retourne l\'email')]
    public function testGetUserIdentifierReturnsEmail(): void
    {
        $this->user->setEmail('identifiant@example.com');
        $this->assertSame('identifiant@example.com', $this->user->getUserIdentifier());
    }

    #[TestDox('getRoles pour un utilisateur standard ne contient pas ROLE_ADMIN')]
    public function testGetRolesForUtilisateur(): void
    {
        $this->user->setRole('utilisateur');
        $roles = $this->user->getRoles();
        $this->assertContains('ROLE_USER', $roles);
        $this->assertNotContains('ROLE_ADMIN', $roles);
    }

    #[TestDox('getRoles pour un admin contient ROLE_USER et ROLE_ADMIN')]
    public function testGetRolesForAdmin(): void
    {
        $this->user->setRole('admin');
        $roles = $this->user->getRoles();
        $this->assertContains('ROLE_USER', $roles);
        $this->assertContains('ROLE_ADMIN', $roles);
    }

    #[TestDox('id est null avant la persistance')]
    public function testIdIsNullByDefault(): void
    {
        $this->assertNull($this->user->getId());
    }

    #[TestDox('Les setters sont fluents (retournent $this)')]
    public function testFluentSetters(): void
    {
        $result = $this->user
            ->setUsername('alice')
            ->setEmail('alice@example.com')
            ->setPassword('secret')
            ->setRole('admin');

        $this->assertSame($this->user, $result);
        $this->assertSame('alice', $this->user->getUsername());
        $this->assertSame('alice@example.com', $this->user->getEmail());
        $this->assertSame('admin', $this->user->getRole());
    }

    #[TestDox('eraseCredentials ne supprime pas le mot de passe stocké')]
    public function testEraseCredentialsDoesNothing(): void
    {
        $this->user->setPassword('secret');
        $this->user->eraseCredentials();
        $this->assertSame('secret', $this->user->getPassword());
    }

    #[TestDox('setCreatedAt accepte un DateTimeImmutable')]
    public function testSetCreatedAt(): void
    {
        $date = new \DateTimeImmutable('2024-01-01 00:00:00');
        $this->user->setCreatedAt($date);
        $this->assertSame($date, $this->user->getCreatedAt());
    }

    /** @return array<string, array{string, bool}> */
    public static function roleAdminProvider(): array
    {
        return [
            'admin'       => ['admin',       true],
            'utilisateur' => ['utilisateur', false],
        ];
    }

    #[DataProvider('roleAdminProvider')]
    #[TestDox('getRoles contient ROLE_ADMIN si et seulement si rôle = admin')]
    public function testAdminRolePresence(string $role, bool $expectAdmin): void
    {
        $this->user->setRole($role);
        $hasAdmin = in_array('ROLE_ADMIN', $this->user->getRoles(), true);
        $this->assertSame($expectAdmin, $hasAdmin);
    }
}
