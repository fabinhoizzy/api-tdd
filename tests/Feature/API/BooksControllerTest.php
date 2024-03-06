<?php

namespace Tests\Feature\API;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;
use App\Models\Book;

class BooksControllerTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_books_get_endpoint(): void
    {
        $books = Book::factory(3)->create();

        $response = $this->get('/api/books');

        $response->assertStatus(200);
        $response->assertJsonCount(3);

        $response->assertJson(function (AssertableJson $json) use ($books) {
           $json->whereAllType([
               '0.id' => 'integer',
               '0.title' => 'string',
               '0.isbn' => 'string',
           ]);

           $json->hasAll(['0.id', '0.title', '0.isbn']);
           $book = $books->first();

           $json->whereAll([
               '0.id' => $book->id,
               '0.title' => $book->title,
               '0.isbn' => $book->isbn,
           ]);
        });
    }

    public function test_get_single_book_endpoint()
    {
        // Criando o livro na base
        $book = Book::factory(1)->createOne();

        // Pegando um livro pela roda
        $response = $this->getJson('/api/books/ '. $book->id);

        // Verifica sem o status è 200
        $response->assertStatus(200);

        // assert em json, para verificar os campos
        $response->assertJson(function (AssertableJson $json) use ($book) {
            // confimando se essas chaves retornaram com sucesso
            $json->hasAll(['id', 'title', 'isbn'])->etc();

            // Verificando os tipos
            $json->whereAllType([
                'id' => 'integer',
                'title' => 'string',
                'isbn' => 'string',
            ]);

            // verificando se os valores batem
            $json->whereAll([
                'id' => $book->id,
                'title' => $book->title,
                'isbn' => $book->isbn,
            ]);
        });
    }

    public function test_post_books_endpoint()
    {
        //criando um objeto de livro, sem salvar em base
        $book = Book::factory(1)->makeOne()->toArray();

        //criando um post com json para roda api/books usando metodo POST
        $response = $this->postJson('/api/books', $book);

        // retorna 201 se foi criado o book
        $response->assertStatus(201);

        $response->assertJson(function (AssertableJson $json) use($book) {

            $json->hasAll('id', 'title', 'created_at', 'updated_at');

            $json->whereAll([
                'title' => $book['title'],
                'isbn' => $book['isbn']
            ])->etc();

        });

    }

    public function test_post_books_should_validate_when_try_created_a_invalid_book()
    {
        //criando um objeto de livro, sem salvar em base
        $book = Book::factory(1)->makeOne()->toArray();

        //criando um post com json para roda api/books usando metodo POST
        $response = $this->postJson('/api/books', $book);

        // retorna 201 se foi criado o book
        $response->assertStatus(201);

        $response->assertJson(function (AssertableJson $json) use($book) {

            $json->hasAll('id', 'title', 'created_at', 'updated_at');

            $json->whereAll([
                'title' => $book['title'],
                'isbn' => $book['isbn']
            ])->etc();
        });
    }

    public function test_put_books_endpoint()
    {
        Book::factory(1)->createOne();

        $book = [
            'title' => 'Atualizando Livro',
            'isbn' => '123456789'
        ];
        //criando um post com json para roda api/books usando metodo POST
        $response = $this->putJson('/api/books/1', $book);

        // retorna 201 se foi criado o book
        $response->assertStatus(200);

        $response->assertJson(function (AssertableJson $json) use($book) {

            $json->hasAll('id', 'title', 'isbn','created_at', 'updated_at');

            $json->whereAll([
                'title' => $book['title'],
                'isbn' => $book['isbn']
            ])->etc();
        });
    }

    public function test_patch_books_endpoint()
    {
        Book::factory(1)->createOne();

        $book = [
            'title' => 'Atualizando Livro patch',
        ];
        //criando um post com json para roda api/books usando metodo POST
        $response = $this->patchJson('/api/books/1', $book);

        // retorna 201 se foi criado o book
        $response->assertStatus(200);

        $response->assertJson(function (AssertableJson $json) use($book) {

            $json->hasAll('id', 'title', 'isbn','created_at', 'updated_at');

            $json->where('title', $book['title'])->etc();
        });
    }

    public function test_delete_books_endpoint()
    {
        Book::factory(1)->createOne();

        $response = $this->deleteJson('/api/books/1');

        $response->assertStatus(204);
    }

}














