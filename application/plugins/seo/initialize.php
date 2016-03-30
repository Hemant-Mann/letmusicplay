<?php

// initialize seo
include("seo.php");

$seo = new SEO(array(
    "title" => "LetMusicPlay",
    "keywords" => "music, music free, music mp3, videos music, search music, free mp3 download, free music download, music listen online, online music player, best music player, music lyrics, new music, play music, top tracks, top artists, discover tracks, music lovers, make playlist, share playlist, share tracks on facebook" ,
    "description" => "A Website made for music lovers. Listen to the latest music, tracks by top artists, search for music, lyrics, songs, or videos. Take the music experience to the next level with our online music player. Find details of any artist or track and save your favorite tracks in our custom playlist. Share the playlist with your friends on facebook.",
    "author" => "Cloudstuff.tech",
    "robots" => "INDEX,FOLLOW",
    "photo" => "http://letmusicplay.in/home/image"
));

Framework\Registry::set("seo", $seo);