<?php

namespace App\Tests\Entity;

use App\Entity\Notification;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

#[CoversClass(Notification::class)]
class NotificationTest extends TestCase
{
    private Notification $notif;

    protected function setUp(): void
    {
        $this->notif = new Notification();
    }

    #[TestDox('Le statut par défaut est "non_lu"')]
    public function testDefaultStatus(): void
    {
        $this->assertSame('non_lu', $this->notif->getStatus());
    }

    #[TestDox('createdAt est initialisé dans le constructeur')]
    public function testCreatedAtIsSetOnConstruct(): void
    {
        $this->assertInstanceOf(\DateTimeImmutable::class, $this->notif->getCreatedAt());
    }

    #[TestDox('id est null avant persistance')]
    public function testIdIsNullByDefault(): void
    {
        $this->assertNull($this->notif->getId());
    }

    #[TestDox('setUserId / getUserId')]
    public function testSetAndGetUserId(): void
    {
        $this->notif->setUserId(5);
        $this->assertSame(5, $this->notif->getUserId());
    }

    #[TestDox('setTypeMessage / getTypeMessage')]
    public function testSetAndGetTypeMessage(): void
    {
        $this->notif->setTypeMessage('suggestion');
        $this->assertSame('suggestion', $this->notif->getTypeMessage());
    }

    #[TestDox('setMessage / getMessage')]
    public function testSetAndGetMessage(): void
    {
        $message = 'Veuillez ajouter une fonctionnalité de recherche avancée.';
        $this->notif->setMessage($message);
        $this->assertSame($message, $this->notif->getMessage());
    }

    #[TestDox('setUsername / getUsername')]
    public function testSetAndGetUsername(): void
    {
        $this->notif->setUsername('alice');
        $this->assertSame('alice', $this->notif->getUsername());
    }

    #[TestDox('setStatus("lu") change le statut')]
    public function testSetStatusToLu(): void
    {
        $this->notif->setStatus('lu');
        $this->assertSame('lu', $this->notif->getStatus());
    }

    #[TestDox('setCreatedAt accepte un DateTimeImmutable')]
    public function testSetCreatedAt(): void
    {
        $date = new \DateTimeImmutable('2025-05-01 09:00:00');
        $this->notif->setCreatedAt($date);
        $this->assertSame($date, $this->notif->getCreatedAt());
    }

    #[TestDox('toArray() contient toutes les clés attendues avec les bonnes valeurs')]
    public function testToArrayStructure(): void
    {
        $this->notif->setUserId(2);
        $this->notif->setTypeMessage('bug');
        $this->notif->setMessage('Le bouton ne fonctionne pas sur mobile.');
        $this->notif->setUsername('bob');

        $array = $this->notif->toArray();

        foreach (['id', 'user_id', 'type_message', 'message', 'username', 'created_at', 'status'] as $key) {
            $this->assertArrayHasKey($key, $array);
        }

        $this->assertSame(2, $array['user_id']);
        $this->assertSame('bug', $array['type_message']);
        $this->assertSame('bob', $array['username']);
        $this->assertSame('non_lu', $array['status']);
    }

    #[TestDox('toArray() formate created_at en Y-m-d H:i:s')]
    public function testToArrayCreatedAtFormat(): void
    {
        $this->notif->setUserId(1);
        $this->notif->setTypeMessage('info');
        $this->notif->setMessage('Message de test suffisamment long.');
        $this->notif->setUsername('user');
        $date = new \DateTimeImmutable('2025-06-15 14:00:00');
        $this->notif->setCreatedAt($date);

        $this->assertSame('2025-06-15 14:00:00', $this->notif->toArray()['created_at']);
    }

    #[TestDox('Les setters sont fluents (retournent $this)')]
    public function testFluentSetters(): void
    {
        $result = $this->notif
            ->setUserId(1)
            ->setTypeMessage('feedback')
            ->setMessage('Super application, continuez comme ça !')
            ->setUsername('carol');

        $this->assertSame($this->notif, $result);
    }

    /** @return array<string, array{string}> */
    public static function statusProvider(): array
    {
        return [
            'non lu' => ['non_lu'],
            'lu'     => ['lu'],
        ];
    }

    #[DataProvider('statusProvider')]
    #[TestDox('setStatus accepte non_lu et lu')]
    public function testValidStatus(string $status): void
    {
        $this->notif->setStatus($status);
        $this->assertSame($status, $this->notif->getStatus());
    }
}
