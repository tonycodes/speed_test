<?php

/**
 * 1. Import the model.
 */
use App\Models\Benefit; // IMPORT THE MODEL

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

/**
 * 2. Name the test.
 */
class BenefitTest extends TestCase
{

    use RefreshDatabase;
    use WithoutMiddleware;

    /**
     * 3. Define your test model.
     *
     * @var
     */
    public $resourceName = 'benefit';
    public $modelTable   = 'benefits';
    public $model;
    public $baseUrl;

    /**
     * 4. Set the required fields for validation, these will be nulled for testing.
     *
     * @var
     */
    public $requiredFields = [
        'name',
        'personal_team'
    ];

    public function setUp(): void
    {

        /**
         * 5. Set the model here, and you're done :)
         */
        $this->model = new Benefit();

        /**
         * IMPORTANT!!!
         * Unless you have specific testing you need to do, or something that is not covered in these tests there is no need to
         * edit anything below this comment.
         */
        $this->baseUrl = 'http://localhost/api/v1/' . $this->resourceName . '/';
        parent::setUp();

    }

    /**
     * @test
     */
    public function it_can_list_resources()
    {

        $count = $this->model->count();
        $this->model->factory(10)->create();

        $response = $this->getJson($this->baseUrl);

        $response
            ->assertJsonCount($count + 10, 'data')
            ->assertSuccessful();

    }

    /**
     * @test
     */
    public function it_can_show_a_resource()
    {

        $exampleResource = $this->model::factory()->create();

        $response = $this->getJson($this->baseUrl . $exampleResource->id);

        $response
            ->assertSuccessful()
            ->assertJson(fn(AssertableJson $json) => $json->hasAll(array_keys($this->model::factory()->make()->toArray()), 'data'));

    }

    /**
     * @test
     */
    public function it_can_store_a_resource()
    {

        $exampleResource = $this->model::factory()->make(['originator_uuid' => 1]);

        $response = $this->postJson($this->baseUrl, $exampleResource);

        $response->assertSuccessful();
        $this->assertDatabaseHas($this->modelTable, $exampleResource);

    }

    /**
     * @test
     */
    public function fields_are_validated_before_storing_resource()
    {

        $invalidFields = [];

        foreach ($this->requiredFields as $field) {

            $invalidFields[$field] = null;

        }

        $exampleResource = $this->model::factory()->make($invalidFields);

        $response = $this->postJson($this->baseUrl, $exampleResource->toArray());

        $response->assertInvalid($this->requiredFields);

    }

    /**
     * @test
     */
    public function it_can_delete_a_resource()
    {

        $exampleResource = $this->model::factory()->create();

        $this->deleteJson($this->baseUrl . $exampleResource->uuid)
            ->assertSuccessful();

    }

    /**
     * @test
     */
    public function it_can_update_a_resource()
    {

        $exampleResource = $this->model::factory()->create();
        $updatedResource = $this->model::factory()->make(['uuid' => $exampleResource->uuid])->toArray();

        $response = $this->putJson($this->baseUrl . $exampleResource->uuid, $updatedResource);

        $response->assertSuccessful();
        $this->assertDatabaseHas($this->modelTable, $updatedResource);

    }

}
