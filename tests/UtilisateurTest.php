<?php

use App\Models\Utilisateur;
use Illuminate\Support\Facades\Hash;
use Laravel\Lumen\Testing\DatabaseTransactions;
use Tests\TestCase;

class UtilisateurTest extends TestCase
{
    //migre la bd lors de l'exécution des tests, puis annule la bd lorsque les tests sont terminés.
    use DatabaseTransactions;

    private $utilisateur = "";
    private $token = "";

    protected function setUp(): void
    {
        parent::setUp();
        //création de l'utilisateur pour le test
        $this->utilisateur = Utilisateur::create([
            'email' => 'test@gmail.com',
            'password' => Hash::make('1234')
        ]);
        $this->utilisateur->password = '1234';
        //authentification
        $this->post('api/login', [
            'email' =>  $this->utilisateur->email,
            'password' =>  $this->utilisateur->password]);

        //permet de s'authentifier et de sauvegarder le token
        $this->token = ($this->response->json()['access_token']);
    }

    public function testRegister()
    {
        $this->post('api/register', [
            'email' => 'testdeux@gmail.com',
            'password' => '1111',
            'password_confirmation' => '1111']);
        $this->assertResponseStatus(200); //Affirme que la réponse a un code d'état 200
    }

    public function testLoginFail()
    {
        $this->post('api/login', [
            'email' => 'test@gmail.com',
            'password' => '1111']);
        $this->assertResponseStatus(401); //Affirme que la réponse a un code d'état 401
        $this->seeJsonContains(['message' => 'pas autorisé']);
    }

    public function testLogin()
    {
        $this->post('api/login', [
            'email' => 'test@gmail.com',
            'password' => '1234']);
        $this->assertResponseStatus(200); //Affirme que la réponse a un code d'état 200
        $this->seeJsonStructure([
            'access_token', 'token_type', 'expires_in'
        ]);
    }

    public function testme(){
        $this->post('api/me', [
            'HTTP_AUTHORIZATION' => "{$this->token}",
            'CONTENT_TYPE' => 'application/ld+json',
            'HTTP_ACCEPT' => 'application/ld+json'
        ]);
        $this->assertResponseStatus(200); //Affirme que la réponse a un code d'état 200
    }

    public  function testrefresh() {
        $this->post('api/refresh', [
            'HTTP_AUTHORIZATION' => "{$this->token}",
            'CONTENT_TYPE' => 'application/ld+json',
            'HTTP_ACCEPT' => 'application/ld+json'
        ]);

        $this->assertResponseStatus(200); //Affirme que la réponse a un code d'état 200
        $this->seeJsonStructure([
            'access_token', 'token_type', 'expires_in'
        ]);
    }

    public function testlogout() {
        $this->post('api/logout', [
            'HTTP_AUTHORIZATION' => "{$this->token}",
            'CONTENT_TYPE' => 'application/ld+json',
            'HTTP_ACCEPT' => 'application/ld+json'
        ]);

        $this->assertResponseStatus(200); //Affirme que la réponse a un code d'état 200
        $this->seeJsonContains(['message' => 'Correctement déconnecté']);
    }

}
