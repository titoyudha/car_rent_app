<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Car;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class CarControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test car index page.
     *
     * @return void
     */
    public function test_car_index()
    {
        $response = $this->get('/cars');

        $response->assertStatus(200);
        $response->assertViewIs('cars.index');
    }

    /**
     * Test car create page.
     *
     * @return void
     */
    public function test_car_create_page()
    {
        $response = $this->get('/cars/create');

        $response->assertStatus(200);
        $response->assertViewIs('cars.create');
    }

    /**
     * Test store car.
     *
     * @return void
     */
    public function test_store_car()
    {
        Storage::fake('cars');

        $data = [
            'name' => 'Toyota Camry',
            'plat' => 'AB123CD',
            'description' => 'Comfortable and stylish sedan.',
            'price' => 50000,
            'status' => 'available',
            'image' => UploadedFile::fake()->image('car.jpg')
        ];

        $response = $this->post('/cars', $data);

        $response->assertRedirect(route('car.index'));
        $this->assertDatabaseHas('cars', ['plat' => 'AB123CD']);
    }

    /**
     * Test edit car page.
     *
     * @return void
     */
    public function test_edit_car_page()
    {
        $car = Car::factory()->create();

        $response = $this->get("/cars/{$car->id}/edit");

        $response->assertStatus(200);
        $response->assertViewIs('cars.edit');
        $response->assertViewHas('car', $car);
    }

    /**
     * Test update car.
     *
     * @return void
     */
    public function test_update_car()
    {
        Storage::fake('cars');

        $car = Car::factory()->create();
        $data = [
            'name' => 'Honda Civic',
            'plat' => 'EF456GH',
            'description' => 'Compact and fuel-efficient.',
            'price' => 40000,
            'status' => 'available',
            'image' => UploadedFile::fake()->image('car.jpg')
        ];

        $response = $this->put("/cars/{$car->id}", $data);

        $response->assertRedirect(route('car.index'));
        $this->assertDatabaseHas('cars', ['plat' => 'EF456GH']);
    }

    /**
     * Test delete car.
     *
     * @return void
     */
    public function test_delete_car()
    {
        Storage::fake('cars');

        $car = Car::factory()->create();
        $car->image = UploadedFile::fake()->image('car.jpg')->store('cars');
        $car->save();

        $response = $this->delete("/cars/{$car->id}");

        $response->assertRedirect(route('car.index'));
        $this->assertDatabaseMissing('cars', ['id' => $car->id]);
    }
}
