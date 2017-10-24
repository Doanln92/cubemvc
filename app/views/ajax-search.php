<?php

$out = array();
$out['post'] = array();
$all = array();
$post_item = array();

foreach($posts as $p){
    $all[] = array(
        'post_image'          => $p->getFeatureImage(),
        "post_image_html"     => '<div class="post-thumbnail"><img src="'.$p->getFeatureImage().'" width="75" height="75" alt=""></div>',
        "ID"                  => $p->id,
        "post_title"          => $p->title,
        "post_author"         => $p->author,
        "post_link"           => $p->getUrl(),
        "post_date_formatted" => $p->getTime("M d, Y")
    );
}

$post_item = array(
    'all' => $all,
    "template"=> '<a href="{post_link}">{post_image_html}<span class="live-search_text">{post_title} </span><p class="post-meta"><span class="post-meta-author"><i class="fa fa-user"></i> {post_author}</span><span class="tie-date"><i class="fa fa-clock-o"></i> {post_date_formatted}</span></p></a>',
    "title"=> "Posts",
    "class_name"=> "live-search_item"
);
$out['post'][] = $post_item;
echo json_encode($out);
?>