<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use DB;

class StatesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::insert("INSERT INTO states (id, name, abbreviation, country, taxes,  status) VALUES
 (1, 'Alaska', 'AK', 'United States',0,1),
 (2, 'Alabama', 'AL', 'United States',0,1),
 (3, 'American Samoa', 'AS', 'United States',0,1),
 (4, 'Arizona', 'AZ', 'United States',0,1),
 (5, 'Arkansas', 'AR', 'United States',0,1),
 (6, 'California', 'CA', 'United States',0,1),
 (7, 'Colorado', 'CO', 'United States',0,1),
 (8, 'Connecticut', 'CT', 'United States',0,1),
 (9, 'Delaware', 'DE', 'United States',0,1),
 (10, 'District of Columbia', 'DC', 'United States',0,1),
 (11, 'Florida', 'FL', 'United States',0,1),
 (12, 'Georgia', 'GA', 'United States',0,1), 
 (13, 'Guam', 'GU', 'United States',0,1),
 (14, 'Hawaii', 'HI', 'United States',0,1),
 (15, 'Idaho', 'ID', 'United States',0,1),
 (16, 'Illinois', 'IL', 'United States',0,1),
 (17, 'Indiana', 'IN', 'United States',0,1),
 (18, 'Iowa', 'IA', 'United States',0,1),
 (19, 'Kansas', 'KS', 'United States',0,1),
 (20, 'Kentucky', 'KY', 'United States',0,1),
 (21, 'Louisiana', 'LA', 'United States',0,1),
 (22, 'Maine', 'ME', 'United States',0,1),
 (23, 'Maryland', 'MD', 'United States',0,1),
 (24, 'Massachusetts', 'MA', 'United States',0,1),
 (25, 'Michigan', 'MI', 'United States',0,1),
 (26, 'Minnesota', 'MN', 'United States',0,1),
 (27, 'Mississippi', 'MS', 'United States',0,1),
 (28, 'Missouri', 'MO', 'United States',0,1),
 (29, 'Montana', 'MT', 'United States',0,1),
 (30, 'Nebraska', 'NE', 'United States',0,1),
 (31, 'Nevada', 'NV', 'United States',0,1),
 (32, 'New Hampshire', 'NH', 'United States',0,1),
 (33, 'New Jersey', 'NJ', 'United States',0,1),
 (34, 'New Mexico', 'NM', 'United States',0,1), 
 (35, 'New York', 'NY', 'United States',0,1),
 (36, 'North Carolina', 'NC', 'United States',0,1),
 (37, 'North Dakota', 'ND', 'United States',0,1),
 (38, 'Northern Mariana Islands', 'MP', 'United States',0,1),
 (39, 'Ohio', 'OH', 'United States',0,1),
 (40, 'Oklahoma', 'OK', 'United States',0,1),
 (41, 'Oregon', 'OR', 'United States',0,1),
 (42, 'Palau', 'PW', 'United States',0,1), 
 (43, 'Pennsylvania', 'PA', 'United States',0,1),
 (44, 'Puerto Rico', 'PR', 'United States',0,1),
 (45, 'Rhode Island', 'RI', 'United States',0,1),
 (46, 'South Carolina', 'SC', 'United States',0,1),
 (47, 'South Dakota', 'SD', 'United States',0,1),
 (48, 'Tennessee', 'TN', 'United States',0,1),
 (49, 'Texas', 'TX', 'United States',0,1), 
 (50, 'Utah', 'UT', 'United States',0,1),
 (51, 'Vermont', 'VT', 'United States',0,1),
 (52, 'Virgin Islands', 'VI', 'United States',0,1),
 (53, 'Virginia', 'VA', 'United States',0,1),
 (54, 'Washington', 'WA', 'United States',0,1),
 (55, 'West Virginia', 'WV', 'United States',0,1),
 (56, 'Wisconsin', 'WI', 'United States',0,1), 
 (57, 'Wyoming', 'WY', 'United States',0,1),
 (58, 'Alberta', 'AB', 'Canada',0,1),
 (59, 'British Columbia', 'BC', 'Canada',0,1),
 (60, 'Manitoba', 'MB', 'Canada',0,1),
 (61, 'New Brunswick', 'NB', 'Canada',0,1),
 (62, 'Newfoundland and Labrador', 'NL', 'Canada',0,1),
 (63, 'Northwest Territories', 'NT', 'Canada',0,1),
 (64, 'Nova Scotia', 'NS', 'Canada',0,1),
 (65, 'Nunavut', 'NU', 'Canada',0,1),
 (66, 'Ontario', 'ON', 'Canada',0,1),
 (67, 'Prince Edward Island', 'PE', 'Canada',0,1),
 (68, 'Québec', 'QC', 'Canada',0,1),
 (69, 'Saskatchewan', 'SK', 'Canada',0,1),
 (70, 'Yukon Territory', 'YT', 'Canada',0,1)"); 

    }
}
