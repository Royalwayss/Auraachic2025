<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\OrdersStatus;

class OrdersStatusesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $status = new OrdersStatus;
        $status->name = "Payment Captured";
        $status->type = "yes";
        $status->status = 1;
        $status->sort = 0;
        $status->save();

        $status = new OrdersStatus;
        $status->name = "Payment Failure";
        $status->type = "yes";
        $status->status = 1;
        $status->sort = 0;
        $status->save();

        $status = new OrdersStatus;
        $status->name = "Shipped";
        $status->type = "yes";
        $status->status = 1;
        $status->sort = 0;
        $status->save();

        $status = new OrdersStatus;
        $status->name = "Delivered";
        $status->type = "yes";
        $status->status = 1;
        $status->sort = 0;
        $status->save();

        $status = new OrdersStatus;
        $status->name = "Pending";
        $status->type = "yes";
        $status->status = 1;
        $status->sort = 0;
        $status->save();

        $status = new OrdersStatus;
        $status->name = "Confirmed";
        $status->type = "yes";
        $status->status = 1;
        $status->sort = 0;
        $status->save();

        $status = new OrdersStatus;
        $status->name = "Cancelled";
        $status->type = "yes";
        $status->status = 1;
        $status->sort = 0;
        $status->save();

    }
}
