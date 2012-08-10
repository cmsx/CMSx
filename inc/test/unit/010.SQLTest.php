<?php

require_once __DIR__.'/../../init.php';

class SQLTest extends PHPUnit_Framework_TestCase
{

  /** @var SQL */
  protected $sql;

  function testByNULLs()
  {
    $sql = $this->sql->where(NULL)->limit(NULL)->orderby(NULL)->groupby(NULL)->columns(NULL)->select();
    $exp = 'SELECT * FROM `pages`';
    $this->assertTrue($sql == $exp, 'Select NULLs: '.$sql);
  }

  function testSelectByID()
  {
    $sql = $this->sql->where(12)->select();
    $exp = 'SELECT * FROM `pages` WHERE `id`=:w_id';
    $this->assertTrue($sql == $exp, 'Select by ID: '.$sql);
  }

  function testSelectByWhereArray()
  {
    $sql = $this->sql->where(array('some'=> 'thing'))->select();
    $exp = 'SELECT * FROM `pages` WHERE `some`=:w_some';
    $this->assertTrue($sql == $exp, 'Select by WhereArray: '.$sql);
    $this->assertTrue($this->sql->getBindedValue(':w_some') == 'thing', 'Select By WhereArray value binding');

    $sql = $this->sql->where(array('`some`="thing"', '`another`>1'))->select();
    $exp = 'SELECT * FROM `pages` WHERE `some`="thing" AND `another`>1';
    $this->assertTrue($sql == $exp, 'Select by WhereArray: '.$sql);

    $sql = $this->sql->where(array('id'=> 12, 'is_active'=> 1))->setNoBind()->select();
    $exp = 'SELECT * FROM `pages` WHERE `id`=12 AND `is_active`=1';
    $this->assertTrue($sql == $exp, 'Select by WhereArray: '.$sql);

    $sql = $this->sql->where(12, true)->setNoBind()->select();
    $exp = 'SELECT * FROM `pages` WHERE `id`=12 AND `is_active`=1';
    $this->assertTrue($sql == $exp, 'Select by WhereArray: '.$sql);
  }

  function testSelectByTRUE()
  {
    $sql = $this->sql->where(true)->select();
    $exp = 'SELECT * FROM `pages` WHERE `is_active`=:w_is_active';
    $this->assertTrue($sql == $exp, 'Select by TRUE SQL Building: '.$sql);
    $this->assertTrue($this->sql->getBindedValue(':w_is_active'), 'Select By TRUE value binding');
  }

  function testColumns()
  {
    $sql = $this->sql->columns(array('id', '`title`'), 'something')->select();
    $exp = 'SELECT `id`, `title`, `something` FROM `pages`';
    $this->assertTrue($sql == $exp, 'Select columns: '.$sql);
  }

  function testOrderBy()
  {
    $sql = $this->sql->orderby(array('id', '`title`'), 'something DESC')->select();
    $exp = 'SELECT * FROM `pages` ORDER BY `id`, `title`, something DESC';
    $this->assertTrue($sql == $exp, 'Select orderby: '.$sql);
  }

  function testPagination()
  {
    $sql = $this->sql->page(3, 20)->select();
    $exp = 'SELECT * FROM `pages` LIMIT 40, 20';
    $this->assertTrue($sql == $exp, 'Select with pagination: '.$sql);
  }

  function testLimit()
  {
    $sql = $this->sql->limit(5)->select();
    $exp = 'SELECT * FROM `pages` LIMIT 5';
    $this->assertTrue($sql == $exp, 'Select with limit: '.$sql);
  }

  function testJoin()
  {
    $sql = $this->sql->table('pages p')
      ->join('users u', 'u.id=p.user_id', 'left')
      ->join('non_users nu', 'nu.id=p.user_id', 'right')
      ->select();
    $exp = 'SELECT * FROM pages p LEFT JOIN users u ON u.id=p.user_id RIGHT JOIN non_users nu ON nu.id=p.user_id';
    $this->assertTrue($sql == $exp, 'Select JOIN: '.$sql);
  }

  function testBinding()
  {
    $this->sql->bindValue('test', 666);
    $this->assertTrue(($this->sql->getBindedValue(':test') == 666), 'Bind Value');

    $this->sql->where(12)->select();
    $this->assertTrue(($this->sql->getBindedValue(':w_id') == 12), 'Bind Where');

    $this->sql->insert(array('some'=> 'thing'));
    $this->assertTrue(($this->sql->getBindedValue(':some') == 'thing'), 'Bind Insert');
  }

  function testUpdate()
  {
    $sql = $this->sql->setNoBind()->where(12)->limit(3)->update(array('foo'=> 'bar', 'another'=> NULL));
    $exp = 'UPDATE `pages` SET `foo`="bar", `another`=NULL WHERE `id`=12 LIMIT 3';
    $this->assertTrue($sql == $exp, 'Update: '.$sql);
  }

  function testDelete()
  {
    $sql = $this->sql->where(12)->limit(3)->delete();
    $exp = 'DELETE FROM `pages` WHERE `id`=:w_id LIMIT 3';
    $this->assertTrue($sql == $exp, 'Delete: '.$sql);
  }

  function testInsert()
  {
    $sql = $this->sql->setNoBind()->insert(array('countme'=> 12, 'foo'=> 'bar', 'another'=> NULL));
    $exp = 'INSERT INTO `pages` (`countme`, `foo`, `another`) VALUES (12, "bar", NULL)';
    $this->assertTrue($sql == $exp, 'Insert: '.$sql);
  }

  function setUp()
  {
    $this->sql = new SQL('pages', '');
  }
}