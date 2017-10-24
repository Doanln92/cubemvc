<?php

namespace Models;

class PostView extends BaseModel{
    public $tableName = 'post_views';
    public function updateView($id)
    {
        if($a = self::where('post_id',$id)->andWhere('view_date',date('Y-m-d'))->first()){
            $a->view_total++;
            $a->update();
        }else{
            $a = new static();
            $a->post_id = $id;
            $a->view_total=1;
            $a->view_date = date('Y-m-d');
            $a->insert();
        }
    }

    public function deleteView($id)
    {
        return $this->mask()->where('post_id',$id)->delete();
    }
}