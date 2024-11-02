<?php

namespace Tests;

use Illuminate\Database\Eloquent\Relations\Relation;
use PHPUnit\Framework\TestCase;
use Tests\Mock\Backed;
use Tests\Mock\Employee;
use Tests\Mock\Unit;
use Tests\Mock\User;
use function Codewiser\ability;
use function Codewiser\relation;
use function Codewiser\tag;

class NamesTest extends TestCase
{
    public function testAbility()
    {
        $this->assertEquals('testAbility', ability([$this, 'testAbility']));
    }

    public function testRelation()
    {
        $this->assertEquals('testRelation', relation([$this, 'testRelation']));
    }

    public function testTag()
    {
        $model = new User();
        $this->assertEquals('User#null', tag($model));
        $model->id = 1;
        $this->assertEquals('User#1', tag($model));
        $model->incrementing = false;
        $this->assertEquals('User', tag($model));

        Relation::morphMap(['user' => User::class]);
        $this->assertEquals('user#null', tag(new User()));

        $pivot = new Employee();
        $this->assertEquals('Employee#null,null', tag($pivot));
        $pivot->setPivotKeys(1, 2);
        $this->assertEquals('Employee#1,2', tag($pivot));
        $pivot->incrementing = true;
        $this->assertEquals('Employee#null', tag($pivot));
        $pivot->id = 3;
        $this->assertEquals('Employee#3', tag($pivot));

        $backed = Backed::test;
        $this->assertEquals('Backed::test', tag($backed));

        $unit = Unit::test;
        $this->assertEquals('Unit::test', tag($unit));

        $this->assertEquals(now()->format('c'), tag(now()));

        $this->assertEquals('string', tag('string'));
        $this->assertEquals(1, tag(1));
    }
}