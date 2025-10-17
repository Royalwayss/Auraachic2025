<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Color;

class ColorsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $color = new Color;
        $color->color_name = "White";
        $color->status = 1;
        $color->save();

        $color = new Color;
        $color->color_name = "Red";
        $color->status = 1;
        $color->save();

        $color = new Color;
        $color->color_name = "Green";
        $color->status = 1;
        $color->save();

        $color = new Color;
        $color->color_name = "Blue";
        $color->status = 1;
        $color->save();

        $color = new Color;
        $color->color_name = "Black";
        $color->status = 1;
        $color->save();

        $color = new Color;
        $color->color_name = "Olive";
        $color->status = 1;
        $color->save();

        $color = new Color;
        $color->color_name = "Orange";
        $color->status = 1;
        $color->save();

        $color = new Color;
        $color->color_name = "Pink";
        $color->status = 1;
        $color->save();

        $color = new Color;
        $color->color_name = "Purple";
        $color->status = 1;
        $color->save();

        $color = new Color;
        $color->color_name = "Multi";
        $color->status = 1;
        $color->save();

        $color = new Color;
        $color->color_name = "Grey";
        $color->status = 1;
        $color->save();

        $color = new Color;
        $color->color_name = "Yellow";
        $color->status = 1;
        $color->save();

        $color = new Color;
        $color->color_name = "Brown";
        $color->status = 1;
        $color->save();

        $color = new Color;
        $color->color_name = "Beige";
        $color->status = 1;
        $color->save();

        $color = new Color;
        $color->color_name = "Peach";
        $color->status = 1;
        $color->save();
    }
}
