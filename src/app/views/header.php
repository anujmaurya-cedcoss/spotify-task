<?php
$class = 'btn btn-primary m-2';
echo $this->tag->linkTo(
    [
        'index/searchHome',
        'Search',
        'class' => $class,
    ]
);

echo $this->tag->linkTo(
    [
        '/recommendation/',
        'Recommendation',
        'class' => $class,
    ]

);

echo $this->tag->linkTo(
    [
        '/showfav/',
        'Show Favorites',
        'class' => $class,
    ]

);

echo $this->tag->linkTo(
    [
        'signup/Logout',
        'Logout',
        'class' => $class,
    ]

);