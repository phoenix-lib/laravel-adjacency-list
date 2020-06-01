<?php

namespace Tests;

use Tests\Models\User;

class EloquentTest extends TestCase
{
    public function testChildren()
    {
        $children = User::find(1)->children;

        $this->assertEquals([2, 3, 4], $children->pluck('id')->all());
    }

    public function testChildrenAndSelf()
    {
        $childrenAndSelf = User::find(1)->childrenAndSelf;

        $this->assertEquals([1, 2, 3, 4], $childrenAndSelf->pluck('id')->all());
    }

    public function testParent()
    {
        $parent = User::find(8)->parent;

        $this->assertEquals(5, $parent->id);
    }

    public function testParentAndSelf()
    {
        $parentAndSelf = User::find(8)->parentAndSelf()->depthFirst()->get();

        $this->assertEquals([8, 5], $parentAndSelf->pluck('id')->all());
    }

    public function testScopeTree()
    {
        $users = User::tree()->orderBy('id')->get();

        $this->assertEquals([1, 2, 3, 4, 5, 6, 7, 8, 9, 11, 12], $users->pluck('id')->all());
        $this->assertEquals([0, 1, 1, 1, 2, 2, 2, 3, 3, 0, 1], $users->pluck('depth')->all());
        $this->assertEquals(['1', '1.2', '1.3', '1.4', '1.2.5', '1.3.6', '1.4.7', '1.2.5.8', '1.3.6.9', '11', '11.12'], $users->pluck('path')->all());
        $this->assertEquals(['user-1', 'user-1/user-2', 'user-1/user-3'], $users->pluck('slug_path')->slice(0, 3)->all());
        $this->assertEquals('users', $users[0]->getTable());
    }

    public function testScopeHasChildren()
    {
        $users = User::hasChildren()->get();

        $this->assertEquals([1, 2, 3, 4, 5, 6, 11], $users->pluck('id')->all());
    }

    public function testScopeHasParent()
    {
        $users = User::hasParent()->get();

        $this->assertEquals([2, 3, 4, 5, 6, 7, 8, 9, 12], $users->pluck('id')->all());
    }

    public function testScopeIsLeaf()
    {
        $users = User::isLeaf()->get();

        $this->assertEquals([7, 8, 9, 12], $users->pluck('id')->all());
    }

    public function testScopeIsRoot()
    {
        $users = User::isRoot()->get();

        $this->assertEquals([1, 11], $users->pluck('id')->all());
    }

    public function testScopeWhereDepth()
    {
        $users = User::find(1)->descendants()->whereDepth(1)->get();

        $this->assertEquals([2, 3, 4], $users->pluck('id')->all());
    }

    public function testScopeWhereDepthWithOperator()
    {
        $users = User::find(1)->descendants()->whereDepth('>', 2)->orderBy('id')->get();

        $this->assertEquals([8, 9], $users->pluck('id')->all());
    }

    public function testScopeBreadthFirst()
    {
        $users = User::tree()->breadthFirst()->orderByDesc('id')->get();

        $this->assertEquals([11, 1, 12, 4, 3, 2, 7, 6, 5, 9, 8], $users->pluck('id')->all());
    }

    public function testScopeDepthFirst()
    {
        $users = User::tree()->depthFirst()->get();

        $this->assertEquals([1, 2, 5, 8, 3, 6, 9, 4, 7, 11, 12], $users->pluck('id')->all());
    }
}
