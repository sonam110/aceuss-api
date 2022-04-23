<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CategoryType;
use App\Models\CategoryMaster;

class CategorySubCat extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            [
                'ICF_kod' => 'd1',
                'name' => 'Lärande och att tillämpa kunskap', 
                'description' => 'Lärande, tillämpning av kunskap som är inlärd, tänkande, problemlösning och beslutsfattande.', 
                'subcat' => [
                        [
                        'ICF_kod' => 'd 138',
                        'name' => 'Att ta reda på information', 
                        'description' => 'Att ta reda på fakta om personer, saker och händelser så som att fråga varför, vad, var, hur att fråga efter namn.', 
                    ],
                    [
                        'ICF_kod' => 'd 155',
                        'name' => 'Att förvärva färdigheter ', 
                        'description' => 'Att utveckla grundläggande och sammansatta
    förmågor att integrera handlingar eller uppgifter
    som att initiera och fullfölja förvärvandet av en
    färdighet såsom att hantera verktyg eller leksaker
    eller spela spel.'],
                    [
                        'ICF_kod' => 'd160',
                        'name' => 'Att fokusera uppmärksamhet', 
                        'description' => 'Att avsiktligt fokusera på specifika stimuli t.ex.
genom att filtrera bort störande ljud.', 
                    ],
                    [
                        'ICF_kod' => 'd175 ',
                        'name' => 'Att lösa problem ', 
                        'description' => 'Att finna lösningar på problem eller situationer
genom att identifiera och analysera frågor,
utveckla möjliga lösningar, utvärdera tänkbara
effekter av lösningar och genomföra en vald
lösning såsom att lösa en konflikt mellan två
personer. ', 
                    ],
                    [
                        'ICF_kod' => 'd177 ',
                        'name' => 'Att fatta beslut', 
                        'description' => 'Att göra ett val mellan alternativ, att förverkliga
valet och utvärdera effekterna av valet såsom att
välja och köpa en specifik sak eller att besluta att ', 
                    ],
                    
                ]
            ],

            [
                'ICF_kod' => 'd2',
                'name' => '', 
                'description' => '', 
                'subcat' => [
                    [
                        'ICF_kod' => '',
                        'name' => '', 
                        'description' => '', 
                    ],
                    
                ]
            ],

            /*[
                'ICF_kod' => 'd2',
                'name' => '', 
                'description' => '', 
                'subcat' => [
                    [
                        'ICF_kod' => '',
                        'name' => '', 
                        'description' => '', 
                    ],
                    
                ]
            ],*/
        ];
        foreach ($data as $key => $category) {
            $categoryMaster = new CategoryMaster;
            $categoryMaster->top_most_parent_id = 1;
            $categoryMaster->created_by = 1;
            $categoryMaster->parent_id = null;
            $categoryMaster->category_type_id = 2;
            $categoryMaster->name = $category['name'];
            $categoryMaster->category_color = "#ff0000";
            $categoryMaster->is_global = '1';
            $categoryMaster->entry_mode = 'Web';
            $categoryMaster->save();
            foreach ($category['subcat'] as $subcat) {
                $subcategory = new CategoryMaster;
                $subcategory->parent_id = $categoryMaster->id;
                $subcategory->top_most_parent_id = 1;
                $subcategory->created_by = 1;
                $subcategory->category_type_id = 2;
                $subcategory->name = $subcat['name'];
                $subcategory->category_color = "#ff0000";
                $subcategory->is_global = '1';
                $subcategory->entry_mode = 'Web';
                $subcategory->save();
            }
        }
        
    }
}
