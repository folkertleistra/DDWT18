<?php
/**
 * Controller
 * User: Folkert Leistra
 * Date: 21-11-2018
 * Time: 16:00
 */

include 'model.php';
/* connect to DB */
$db = connect_db('localhost', 'ddwt18_week1', 'ddwt18', 'ddwt18');
/* counts the amount of series in the database */
$serie_count = count_series($db);
/* get series from database */
$series = get_series($db);
/* create table with series from database */
$table = get_serie_table($series);

/* Landing page */
if (new_route('/DDWT18/week1/', 'get')) {
    /* Page info */
    $page_title = 'Home';
    $breadcrumbs = get_breadcrumbs([
        'DDWT18' => na('/DDWT18/', False),
        'Week 1' => na('/DDWT18/week1/', False),
        'Home' => na('/DDWT18/week1/', True)
    ]);
    $navigation = get_navigation([
        'Home' => na('/DDWT18/week1/', True),
        'Overview' => na('/DDWT18/week1/overview/', False),
        'Add Series' => na('/DDWT18/week1/add/', False)
    ]);

    /* Page content */
    $right_column = use_template('cards');
    $page_subtitle = 'The online platform to list your favorite series';
    $page_content = 'On Series Overview you can list your favorite series. You can see the favorite series of all Series Overview users. By sharing your favorite series, you can get inspired by others and explore new series.';

    /* Choose Template */
    include use_template('main');
}

/* Overview page */
elseif (new_route('/DDWT18/week1/overview/', 'get')) {
    /* Page info */
    $page_title = 'Overview';
    $breadcrumbs = get_breadcrumbs([
        'DDWT18' => na('/DDWT18/', False),
        'Week 1' => na('/DDWT18/week1/', False),
        'Overview' => na('/DDWT18/week1/overview', True)
    ]);
    $navigation = get_navigation([
        'Home' => na('/DDWT18/week1/', False),
        'Overview' => na('/DDWT18/week1/overview', True),
        'Add Series' => na('/DDWT18/week1/add/', False)
    ]);

    /* Page content */
    $right_column = use_template('cards');
    $page_subtitle = 'The overview of all series';
    $page_content = 'Here you find all series listed on Series Overview.';
    $left_content = $table;
    /* Choose Template */
    include use_template('main');
}

/* Single Serie */
elseif (new_route('/DDWT18/week1/serie/', 'get')) {
    /* Get series from db */
    $serie_id = $_GET['serie_id'];
    $serie_info = get_series_info($db, $serie_id);
    /* assign serie info to variables */
    $serie_name = $serie_info['name'];
    $serie_abstract = $serie_info['abstract'];
    $nbr_seasons = $serie_info['seasons'];
    $creators = $serie_info['creator'];

    /* Page info */
    $page_title = $serie_name;
    $breadcrumbs = get_breadcrumbs([
        'DDWT18' => na('/DDWT18/', False),
        'Week 1' => na('/DDWT18/week1/', False),
        'Overview' => na('/DDWT18/week1/overview/', False),
        $serie_name => na('/DDWT18/week1/serie/', True)
    ]);
    $navigation = get_navigation([
        'Home' => na('/DDWT18/week1/', False),
        'Overview' => na('/DDWT18/week1/overview', True),
        'Add Series' => na('/DDWT18/week1/add/', False)
    ]);

    /* Page content */
    $right_column = use_template('cards');
    $page_subtitle = sprintf("Information about %s", $serie_name);
    $page_content = $serie_abstract;

    /* Choose Template */
    include use_template('serie');
}

/* Add serie GET */
elseif (new_route('/DDWT18/week1/add/', 'get')) {
    /* Page info */
    $page_title = 'Add Series';
    $breadcrumbs = get_breadcrumbs([
        'DDWT18' => na('/DDWT18/', False),
        'Week 1' => na('/DDWT18/week1/', False),
        'Add Series' => na('/DDWT18/week1/new/', True)
    ]);
    $navigation = get_navigation([
        'Home' => na('/DDWT18/week1/', False),
        'Overview' => na('/DDWT18/week1/overview', False),
        'Add Series' => na('/DDWT18/week1/add/', True)
    ]);

    /* Page content */
    $right_column = use_template('cards');
    $page_subtitle = 'Add your favorite series';
    $page_content = 'Fill in the details of you favorite series.';
    $submit_btn = "Add Series";
    $form_action = '/DDWT18/week1/add/';

    /* Choose Template */
    include use_template('new');
}

/* Add serie POST */
elseif (new_route('/DDWT18/week1/add/', 'post')) {
    /* get serie info */
    $serie_info = array('name' => $_POST['Name'], 'creator' => $_POST['Creator'],
        'seasons' => $_POST['Seasons'], 'abstract' => $_POST['Abstract']);
    $feedback = add_series($db,$serie_info);
    $error_msg = get_error($feedback);
    /* page info */
    $page_title = 'Add Series';
    $breadcrumbs = get_breadcrumbs([
        'DDWT18' => na('/DDWT18/', False),
        'Week 1' => na('/DDWT18/week1/', False),
        'Add Series' => na('/DDWT18/week1/add/', True)
    ]);
    $navigation = get_navigation([
        'Home' => na('/DDWT18/week1/', False),
        'Overview' => na('/DDWT18/week1/overview', False),
        'Add Series' => na('/DDWT18/week1/add/', True)
    ]);

    /* Page content */
    $right_column = use_template('cards');
    $page_subtitle = 'Add your favorite series';
    $page_content = 'Fill in the details of you favorite series.';
    $submit_btn = "Add Series";
    $form_action = '/DDWT18/week1/add/';

    include use_template('new');
}

/* Edit serie GET */
elseif (new_route('/DDWT18/week1/edit/', 'get')) {
    /* Get serie info from db */
    $serie_id = $_GET['serie_id'];
    $serie_info = get_series_info($db, $serie_id);
    /* assign sere info to variables */
    $serie_name = $serie_info['name'];
    $serie_abstract = $serie_info['abstract'];
    $nbr_seasons = $serie_info['seasons'];
    $creators = $serie_info['creator'];

    /* Page info */
    $page_title = 'Edit Series';
    $breadcrumbs = get_breadcrumbs([
        'DDWT18' => na('/DDWT18/', False),
        'Week 1' => na('/DDWT18/week1/', False),
        sprintf("Edit Series %s", $serie_name) => na('/DDWT18/week1/new/', True)
    ]);
    $navigation = get_navigation([
        'Home' => na('/DDWT18/week1/', False),
        'Overview' => na('/DDWT18/week1/overview', False),
        'Add Series' => na('/DDWT18/week1/add/', False)
    ]);

    /* Page content */
    $right_column = use_template('cards');
    $page_subtitle = sprintf("Edit %s", $serie_name);
    $page_content = 'Edit the series below.';
    $submit_btn = "Edit Series";
    $form_action = '/DDWT18/week1/edit';

    /* Choose Template */
    include use_template('new');
}

/* Edit serie POST */
elseif (new_route('/DDWT18/week1/edit/', 'post')) {
    /* Get serie info from db */

    $serie_id = $_POST['serie_id'];
    $new_serie_info = array('id' => $_POST['serie_id'], 'name' => $_POST['Name'], 'creator' => $_POST['Creator'],
        'seasons' => $_POST['Seasons'], 'abstract' => $_POST['Abstract']);
    $feedback = update_series($db, $new_serie_info);
    $error_msg = get_error($feedback);

    $serie_name = $new_serie_info['name'];
    $serie_abstract = $new_serie_info['abstract'];
    $nbr_seasons = $new_serie_info['seasons'];
    $creators = $new_serie_info['creator'];

    /* Page info */
    $page_title = $serie_name;
    $breadcrumbs = get_breadcrumbs([
        'DDWT18' => na('/DDWT18/', False),
        'Week 1' => na('/DDWT18/week1/', False),
        'Overview' => na('/DDWT18/week1/overview/', False),
        $serie_name => na('/DDWT18/week1/serie/', True)
    ]);
    $navigation = get_navigation([
        'Home' => na('/DDWT18/week1/', False),
        'Overview' => na('/DDWT18/week1/overview', False),
        'Add Series' => na('/DDWT18/week1/add/', False)
    ]);

    /* Page content */
    $right_column = use_template('cards');
    $page_subtitle = sprintf("Information about %s", $serie_name);
    $page_content = $serie_abstract;

    /* Choose Template */
    include use_template('serie');
}

/* Remove serie */
elseif (new_route('/DDWT18/week1/remove/', 'post')) {
    /* Remove serie in database */
    $serie_id = $_POST['serie_id'];
    $serie_info = get_series_info($db, $serie_id);
    $feedback = remove_serie($db, $serie_info);
    $error_msg = get_error($feedback);

    /* Page info */
    $page_title = 'Overview';
    $breadcrumbs = get_breadcrumbs([
        'DDWT18' => na('/DDWT18/', False),
        'Week 1' => na('/DDWT18/week1/', False),
        'Overview' => na('/DDWT18/week1/overview', True)
    ]);
    $navigation = get_navigation([
        'Home' => na('/DDWT18/week1/', False),
        'Overview' => na('/DDWT18/week1/overview', True),
        'Add Series' => na('/DDWT18/week1/add/', False)
    ]);

    /* Page content */
    $right_column = use_template('cards');
    $page_subtitle = 'The overview of all series';
    $page_content = 'Here you find all series listed on Series Overview.';
    $left_content = $table;

    /* Choose Template */
    include use_template('main');
}

else {
    http_response_code(404);
}