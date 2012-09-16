<?php

require_once __DIR__.'/../init.php';

class URLTest extends PHPUnit_Framework_TestCase
{
  function testParsingStrings () {
    $exp = array( array(1=>'test', 'me'), array() );
    $act = URL::Parse('/test/me/');
    $this->assertEquals( $exp, $act, 'Two' );

    $exp = array( array(1=>'test', 'me'), array('id'=>12, 'some'=>'thing') );
    $act = URL::Parse('/test/me/id:12/some:thing/');
    $this->assertEquals( $exp, $act, 'Two & two param' );

    $exp = array( array(1=>'test', 'me'), array('id'=>array(12,13)) );
    $act = URL::Parse('/test/me/id:12/id:13/');
    $this->assertEquals( $exp, $act, 'Two & one multi param' );

    $exp = array( array(1=>'русский', 'язык'), array() );
    $act = URL::Parse('/русский/язык/');
    $this->assertEquals( $exp, $act, 'Russian lang' );

    $exp = array( array(1=>'test', 'me', '#some'), array() );
    $act = URL::Parse('/test/me/#some');
    $this->assertEquals( $exp, $act, 'URL with #anchor' );

    $exp = array( array(1=>'test', 'me', 'file.txt'), array() );
    $act = URL::Parse('/test/me/file.txt');
    $this->assertEquals( $exp, $act, 'URL with file and extension' );
  }
}