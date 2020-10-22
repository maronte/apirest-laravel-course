<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Buyer;
use App\Seller;
use App\Transaction;
use Faker\Generator as Faker;

$factory->define(Transaction::class, function (Faker $faker) {

    $vendedor = Seller::has('products')->get()->random();
    $comprador = Buyer::all()->except($vendedor->id)->random();

    return [
        'quantity' => $faker->numberBetween(1, 4),
        'buyer_id' => $comprador->id,
        'product_id' => $vendedor->products->random()->id,
    ];
});
