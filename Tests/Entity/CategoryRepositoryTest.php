<?php

namespace Bundle\ForumBundle\Tests\Entity;

use Bundle\ForumBundle\Test\WebTestCase;
use Bundle\ForumBundle\Entity\Category;
use Bundle\ForumBundle\Entity\CategoryRepository;

class CategoryRepositoryTest extends WebTestCase
{

    public function setUp()
    {
        // remove all categories before each test
        $em = $this->getService('Doctrine.ORM.EntityManager');
        $repository = $em->getRepository('ForumBundle:Category');

        $categories = $repository->findAll();
        foreach ($categories as $category) {
            $em->remove($category);
        }
        $em->flush();
    }

    public function testFindAll()
    {
        $em = $this->getService('Doctrine.ORM.EntityManager');
        $repository = $em->getRepository('ForumBundle:Category');

        // there is no category
        $categories = $repository->findAll();
        
        $this->assertInternalType('array', $categories, '::findAll return an array even if there is no category');
        $this->assertEquals(0, count($categories), '::findAll return an empty array if there is no category');

        // add some categories
        $category1 = new Category();
        $category1->setName('Category 1');
        $category1->setPosition(1);
        $em->persist($category1);

        $category3 = new Category();
        $category3->setName('Category 3');
        $category3->setPosition(3);
        $em->persist($category3);

        $category2 = new Category();
        $category2->setName('Category 2');
        $category2->setPosition(2);
        $em->persist($category2);

        $em->flush();

        $categories = $repository->findAll();

        $this->assertInternalType('array', $categories, '::findAll return an array of categories');
        $this->assertEquals(3, count($categories), '::findAll find ALL categories');
        
        $this->assertEquals($category1, $categories[0], '::findAll return categories in the right order');
        $this->assertEquals($category2, $categories[1], '::findAll return categories in the right order');
        $this->assertEquals($category3, $categories[2], '::findAll return categories in the right order');
    }

    public function testFindOneBySlug()
    {
        $em = $this->getService('Doctrine.ORM.EntityManager');
        $repository = $em->getRepository('ForumBundle:Category');

        $this->assertEquals(null, $repository->findOneBySlug('there-is-no-category-matching-to-this-slug'), '::findOneBySlug returns NULL if the specified slug does not match any category');

        $category = new Category();
        $category->setName('Foo bar');

        $em->persist($category);
        $em->flush();

        $foundCategory = $repository->findOneBySlug($category->getSlug());

        $this->assertNotEmpty($foundCategory, '::findOneBySlug find a category for the specified slug');
        $this->assertInstanceOf('Bundle\ForumBundle\Entity\Category', $foundCategory, '::findOneBySlug returns a Category instance');
        $this->assertEquals($category, $foundCategory, '::findOneBySlug find the good category for the specified slug');
    }
}