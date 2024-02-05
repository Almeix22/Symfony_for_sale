<?php

namespace App\Story;

use App\Factory\CategoryFactory;
use Zenstruck\Foundry\Story;

final class CategoryStory extends Story
{
    public function build(): void
    {
        $categoryFile = file('src/data/category.txt');
        $this->addState('category_without_advertisement', CategoryFactory::createOne(['name' => $categoryFile[0]]));
        array_shift($categoryFile);
        foreach ($categoryFile as $file) {
            $this->addToPool('categories', CategoryFactory::createOne(['name' => $file]));
        }
    }
}
