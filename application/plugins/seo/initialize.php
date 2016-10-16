<?php

// initialize seo
include("seo.php");

$seo = new SEO(array(
    "title" => "LetMusicPlay | Download Video Songs | Download mp3 songs High Quality",
    "keywords" => "dwonload music, dwonload music free, dwonload music mp3, dwonload videos songs free, search music, free mp3 download, free music download, music listen online, online music player, best music player, music lyrics, new music, play music, top tracks, top artists, discover tracks, music lovers, make playlist, share playlist, share tracks on facebook" ,
    "description" => "Download Video Songs for free and mp3 songs high qulaity. Your favourite place to download songs",
    "robots" => "INDEX,FOLLOW",
    "author" => "Hemant Mann",
    "photo" => "http://letmusicplay.in/public/assets/img/letmusicplay.png"
));

Framework\Registry::set("seo", $seo);