<?php

namespace App\Service;

class Isbn
{
    private $isbnList = array(
        '9787426660698', '9784849037151', '9788809476356', '9781283885966', '9781996579350',
        '9784246380256', '9783691963885', '9788956929293', '9786231938756', '9786347958303',
        '9782363323828', '9782350414294', '9783623754284', '9780653311302', '9783428417926',
        '9780683441208', '9780874644791', '9780102092837', '9789509588110', '9787663448745',
    );

    public function generate(int $num=1) {
        $num = ($num > count($this->isbnList))? count($this->isbnList) : $num;
        $isbns = array();
        while($num > 0) {
            $key = array_rand($this->isbnList);
            $isbns[] = $this->isbnList[$key];
            unset($this->isbnList[$key]);
            $num--;
        }

        return $isbns;
    }
}