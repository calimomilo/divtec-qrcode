<?php

namespace Tests;

use App\Models\Qrcode;
use App\Models\Utilisateur;
use Illuminate\Support\Facades\Hash;
use Laravel\Lumen\Testing\DatabaseTransactions;

class QrcodeTest extends TestCase
{

    use DatabaseTransactions;

    private $qrcodes = "";
    private $utilisateur = "";
    private $token = "";

    /**
     * Affectation des variables avec la factory
     * Cette méthode est lancée avant l'exécution des tests
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->qrcodes = Qrcode::factory()->count(2)->create();

        $this->utilisateur = Utilisateur::create(
            [
                'email' => 'test@test.com',
                'password' => Hash::make('1234')
            ]);
        $this->utilisateur->password = '1234';

        Qrcode::findOrFail($this->qrcodes[0]->id)->update(['utilisateur_id' => $this->utilisateur->id]);
        Qrcode::findOrFail($this->qrcodes[1]->id)->update(['utilisateur_id' => $this->utilisateur->id]);

        $this->post('api/login', [
            'email' => $this->utilisateur->email,
            'password' => $this->utilisateur->password
        ]);

        $this->token = ($this->response->json()['access_token']);
    }

    public function testShowAllQrcodes()
    {
        $this->get('api/qrcodes', [
            'HTTP_AUTHORIZATION' => "{$this->token}",
            'CONTENT_TYPE' => 'application/ld+json',
            'HTTP_ACCEPT' => 'application/ld+json'
        ]);
        $this->assertResponseOk();
    }

    public function testShowOneQrcode()
    {
        $this->get('api/qrcodes/'.$this->qrcodes[0]->id,[
            'HTTP_AUTHORIZATION' => "{$this->token}",
            'CONTENT_TYPE' => 'application/ld+json',
            'HTTP_ACCEPT' => 'application/ld+json'
        ]);
        $this->assertResponseOk();
    }

    public function testCreateQrcode()
    {
        $qrcode = Qrcode::factory()->make();

        $this->post('api/qrcodes',
            [
            'nom' => $qrcode->nom,
            'image' => $qrcode->image,
            'code_id' => $qrcode->code_id,
            'lien_redir' => $qrcode->lien_redir,
            'HTTP_AUTHORIZATION' => "{$this->token}",
            'CONTENT_TYPE' => 'application/ld+json',
            'HTTP_ACCEPT' => 'application/ld+json'
            ]);
        $this->assertResponseOk();
        $this->seeJsonContains(
            [
                'nom' => $qrcode->nom,
                'image' => $qrcode->image,
                'code_id' => $qrcode->code_id,
                'lien_redir' => $qrcode->lien_redir
            ]);
        $this->seeInDatabase('qrcodes',
            [
                'nom' => $qrcode->nom,
                'image' => $qrcode->image,
                'code_id' => $qrcode->code_id,
                'lien_redir' => $qrcode->lien_redir
            ]);
    }

    public function testUpdateQrcode()
    {
        $qrcode = $this->qrcodes[0];
        $newqrcode = [
            'nom' => $qrcode->nom.'test',
            'image' => $qrcode->image,
            'code_id' => $qrcode->code_id,
            'lien_redir' => $qrcode->lien_redir
        ];

        $mergeParam = array_merge($newqrcode, [
            'HTTP_AUTHORIZATION' => "{$this->token}",
            'CONTENT_TYPE' => 'application/ld+json',
            'HTTP_ACCEPT' => 'application/ld+json'
        ]);

        $this->put('api/qrcodes/'.$qrcode->id, $mergeParam);
        $this->assertResponseOk();
        $this->seeJsonContains($newqrcode);
        $this->seeInDatabase('qrcodes', $newqrcode);
    }

    public function testDeleteQrcode()
    {
        $this->delete('api/qrcodes/'.$this->qrcodes[0]->id, [
            'HTTP_AUTHORIZATION' => "{$this->token}",
            'CONTENT_TYPE' => 'application/ld+json',
            'HTTP_ACCEPT' => 'application/ld+json'
        ]);
        $this->assertResponseStatus(204);
        $this->notSeeInDatabase('qrcodes', [
            'id' => $this->qrcodes[0]->id
        ]);
    }

    public function testCreateFailRequired()
    {
        $qrcode = Qrcode::factory()->make();

        $this->post('api/qrcodes',
        [
            // 'nom' => $qrcode->nom,
            'image' => $qrcode->image,
            'code_id' => $qrcode->code_id,
            'lien_redir' => $qrcode->lien_redir,
            'HTTP_AUTHORIZATION' => "{$this->token}",
            'CONTENT_TYPE' => 'application/ld+json',
            'HTTP_ACCEPT' => 'application/ld+json'
        ]);
        $this->assertResponseStatus(422);
        $this->notSeeInDatabase('qrcodes', ['id' => $qrcode->id]);
    }

    public function testNotFoundQrcode()
    {
        $qrcode = Qrcode::factory()->make();

        $this->put('api/qrcodes/tt',
        [
            'nom' => $qrcode->nom,
            'image' => $qrcode->image,
            'code_id' => $qrcode->code_id,
            'lien_redir' => $qrcode->lien_redir,
            'HTTP_AUTHORIZATION' => "{$this->token}",
            'CONTENT_TYPE' => 'application/ld+json',
            'HTTP_ACCEPT' => 'application/ld+json'
        ]);
        $this->assertResponseStatus(404);
        $this->seeJsonContains(['message' => 'qrcode not found']);
    }

    public function testNotOwnerShowOneQrcode()
    {
        $qrcode = Qrcode::factory()->create();

        $this->get('api/qrcodes/'.$qrcode->id, [
            'HTTP_AUTHORIZATION' => "{$this->token}",
            'CONTENT_TYPE' => 'application/ld+json',
            'HTTP_ACCEPT' => 'application/ld+json'
        ]);

        $this->assertResponseStatus(401);
        $this->seeJsonContains(['message' => 'Not the owner']);
    }

}
