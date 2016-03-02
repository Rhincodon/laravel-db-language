<?php

namespace Rhinodontypicus\DBLanguage\Test;

class DBLanguageTest extends TestCase
{
    /**
     * @test
     */
    public function it_return_db_language_instance()
    {
        $this->assertInstanceOf("Rhinodontypicus\\DBLanguage\\DbLanguage", db_language());
    }

    /**
     * @test
     */
    public function it_return_loaded_language_model_and_fields()
    {
        db_language()->load($this->languages[0]->id);

        $this->assertEquals($this->languages[0]->id, db_language()->language('id'));
    }

    /**
     * @test
     */
    public function it_create_default_value_for_language_constant_for_loaded_language()
    {
        db_language()->load($this->languages[0]->id);
        $constantValue = db_language("site::test", "Test");

        $this->assertEquals($constantValue, "Test");
        $this->seeInDatabase("language_constants", [
            "name"  => "test",
            "group" => "site",
        ]);
        $this->seeInDatabase("language_constant_values", [
            "language_id" => $this->languages[0]->id,
            "value"       => "Test",
        ]);
        $this->dontSeeInDatabase("language_constant_values", ["language_id" => $this->languages[1]->id]);
    }

    /**
     * @test
     */
    public function it_retrieve_value_by_name_and_group()
    {
        db_language()->load($this->languages[0]->id);
        $constantValue = db_language("site::test", "Test");

        $this->assertEquals("Test", $constantValue);

        $constantValue = db_language("site::test");

        $this->assertEquals("Test", $constantValue);
    }

    /**
     * @test
     */
    public function it_loads_only_constants_for_specified_group()
    {
        db_language()->load($this->languages[0]->id);
        db_language("site::test", "Test");
        db_language("cms::test", "Test2");

        db_language()->load($this->languages[0]->id, "site");

        $this->assertEquals("cms::test", db_language()->get('cms::test'));
        $this->assertEquals("Test", db_language()->get('site::test'));
    }

    /**
     * @test
     */
    public function it_does_not_create_constant_twice()
    {
        db_language()->load($this->languages[0]->id);
        db_language("site::const", "Test");
        db_language("site::const", "Test");

        $this->seeInDatabase("language_constants", [
            "id" => 1,
            "name"  => "const",
            "group" => "site",
        ]);

        $this->dontSeeInDatabase("language_constants", [
            "id" => 2,
            "name"  => "const",
            "group" => "site",
        ]);
    }
}
