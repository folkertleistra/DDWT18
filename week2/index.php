<?php
/**
 * Controller
 * User: Folkert Leistra
 * Date: 28-11-2018
 * Time: 17:30
 */

include 'model.php';

/* Connect to DB */
$db = connect_db('localhost', 'ddwt18_week2', 'ddwt18','ddwt18');


/* redundant code */
$nbr_series = count_series($db);
$nbr_users = count_users($db);
$right_column = use_template('cards');

$template =
    Array(
    1 => Array(
        'name' => 'Home',
        'url' => '/DDWT18/week2/'
    ),
    2 => Array(
        'name' => 'Overview',
        'url' => '/DDWT18/week2/overview/'
    ),
    3 => Array(
        'name' => 'My Account',
        'url' => '/DDWT18/week2/myaccount/'
    ),
    4 => Array(
        'name' => 'Register',
        'url' => '/DDWT18/week2/register/'
    ),
    5 => Array(
        'name'=> 'Login',
        'url' => '/DDWT18/week2/login/'
    )
    );

/* Landing page */
if (new_route('/DDWT18/week2/', 'get')) {

    /* Page info */
    $page_title = 'Home';
    $breadcrumbs = get_breadcrumbs([
        'DDWT18' => na('/DDWT18/', False),
        'Week 2' => na('/DDWT18/week2/', False),
        'Home' => na('/DDWT18/week2/', True)
    ]);
    $navigation = get_navigation($template, 1);

    /* Page content */
    $page_subtitle = 'The online platform to list your favorite series';
    $page_content = 'On Series Overview you can list your favorite series. You can see the favorite series of all Series Overview users. By sharing your favorite series, you can get inspired by others and explore new series.';

    /* Get error msg from POST route */
    if ( isset($_GET['error_msg']) ) {
        $error_msg = get_error($_GET['error_msg']);
    }

    /* Choose Template */
    include use_template('main');
}

/* Overview page */
elseif (new_route('/DDWT18/week2/overview/', 'get')) {

    /* Page info */
    $page_title = 'Overview';
    $breadcrumbs = get_breadcrumbs([
        'DDWT18' => na('/DDWT18/', False),
        'Week 2' => na('/DDWT18/week2/', False),
        'Overview' => na('/DDWT18/week2/overview', True)
    ]);
    $navigation = get_navigation($template, 2);

    /* Page content */
    $page_subtitle = 'The overview of all series';
    $page_content = 'Here you find all series listed on Series Overview.';
    $left_content = get_serie_table(get_series($db), $db);

    /* Get error msg from POST route */
    if ( isset($_GET['error_msg']) ) {
        $error_msg = get_error($_GET['error_msg']);
    }

    /* Choose Template */
    include use_template('main');
}

/* Single Serie */
elseif (new_route('/DDWT18/week2/serie/', 'get')) {
    /* get information about a serie from the database */
    $serie_id = $_GET['serie_id'];
    $serie_info = get_serieinfo($db, $serie_id);
    $user_id = $serie_info['user'];

    /* check if logged in */
    if ( !check_login() ) {
        $display_buttons = False;
    } else {
        /* check if user that is logged in is the same as the user that creates the series */
        if (get_user_id() === $user_id) {
            $display_buttons = True;

        } else {
        $display_buttons = False;
        }
    }

    /* Page info */
    $page_title = $serie_info['name'];
    $breadcrumbs = get_breadcrumbs([
        'DDWT18' => na('/DDWT18/', False),
        'Week 2' => na('/DDWT18/week2/', False),
        'Overview' => na('/DDWT18/week2/overview/', False),
        $serie_info['name'] => na('/DDWT18/week2/serie/?serie_id='.$serie_id, True)
    ]);

    /* Page content */
    $right_column = use_template('cards');
    $page_subtitle = sprintf("Information about %s", $serie_info['name']);
    $page_content = $serie_info['abstract'];
    $nbr_seasons = $serie_info['seasons'];
    $creators = $serie_info['creator'];
    $navigation = get_navigation($template, 2);

    $added_by = get_name($db, $user_id);

    /* Get error msg from POST route */
    if ( isset($_GET['error_msg']) ) {
        $error_msg = get_error($_GET['error_msg']);
    }

    /* Choose Template */
    include use_template('serie');
}

/* Add serie GET */
elseif (new_route('/DDWT18/week2/add/', 'get')) {

    /* Check if logged in */
    if ( !check_login() ) {
        redirect('/DDWT18/week2/login/');
    }
    /* Page info */
    $page_title = 'Add Series';
    $breadcrumbs = get_breadcrumbs([
        'DDWT18' => na('/DDWT18/', False),
        'Week 2' => na('/DDWT18/week2/', False),
        'Add Series' => na('/DDWT18/week2/new/', True)
    ]);

    /* Page content */
    $page_subtitle = 'Add your favorite series';
    $page_content = 'Fill in the details of you favorite series.';
    $submit_btn = "Add Series";
    $form_action = '/DDWT18/week2/add/';
    $navigation = get_navigation($template, 2);

    /* Get error msg from POST route */
    if ( isset($_GET['error_msg']) ) {
        $error_msg = get_error($_GET['error_msg']);
    }

    /* Choose Template */
    include use_template('new');
}

/* Add serie POST */
elseif (new_route('/DDWT18/week2/add/', 'post')) {
    /* Check if logged in */
    if ( !check_login() ) {
        redirect('/DDWT18/week2/login/');
    }
    /* Add serie to database */
    $feedback = add_serie($db, $_POST);
    /* Redirect to serie GET route */
    redirect(sprintf('/DDWT18/week2/overview/?error_msg=%s',
        json_encode($feedback)));
}

/* Edit serie GET */
elseif (new_route('/DDWT18/week2/edit/', 'get')) {
    /* Check if logged in */
    if ( !check_login() ) {
        redirect('/DDWT18/week2/login/');
    }
    /* Get serie info from db */
    $serie_id = $_GET['serie_id'];
    $serie_info = get_serieinfo($db, $serie_id);

    /* Page info */
    $page_title = 'Edit Series';
    $breadcrumbs = get_breadcrumbs([
        'DDWT18' => na('/DDWT18/', False),
        'Week 2' => na('/DDWT18/week2/', False),
        sprintf("Edit Series %s", $serie_info['name']) => na('/DDWT18/week2/new/', True)
    ]);

    /* Page content */
    $page_subtitle = sprintf("Edit %s", $serie_info['name']);
    $page_content = 'Edit the series below.';
    $submit_btn = "Edit Series";
    $form_action = '/DDWT18/week2/edit/';

    $navigation = get_navigation($template, 2);

    /* Get error msg from POST route */
    if ( isset($_GET['error_msg']) ) {
        $error_msg = get_error($_GET['error_msg']);
    }

    /* Choose Template */
    include use_template('new');
}

/* Edit serie POST */
elseif (new_route('/DDWT18/week2/edit/', 'post')) {
    /* Check if logged in */
    if ( !check_login() ) {
        redirect('/DDWT18/week2/login/');
    }
    /* edit serie in database */

    $feedback = update_series($db, $_POST);
    /* Redirect to serie GET route */
    redirect(sprintf('/DDWT18/week2/overview/?error_msg=%s',
        json_encode($feedback)));
}


/* Remove serie POST */
elseif (new_route('/DDWT18/week2/remove/', 'post')) {
    /* Check if logged in */
    if ( !check_login() ) {
        redirect('/DDWT18/week2/login/');
    }
    /* remove serie from database */

    $serie_id = $_POST['serie_id'];
    $serie_info = get_serieinfo($db, $serie_id);
    $feedback = remove_serie($db, $serie_info);
    /* Redirect to serie GET route */
    redirect(sprintf('/DDWT18/week2/overview/?error_msg=%s',
        json_encode($feedback)));
}

/* my account GET */
elseif (new_route('/DDWT18/week2/myaccount/', 'get')) {
    $user_id = get_user_id();
    $user = get_name($db, $user_id);
    /* Check if logged in */
    if ( !check_login() ) {
        redirect('/DDWT18/week2/login/');
    }
    /* Page info */
    $page_title = 'My account';
    $breadcrumbs = get_breadcrumbs([
        'DDWT18' => na('/DDWT18/', False),
        'Week 2' => na('/DDWT18/week2/', False),
        'myaccount' => na('DDWT18/week2/myaccount', True)
    ]);
    $navigation = get_navigation($template, 3);

    /* Page content */
    $page_subtitle = 'Information about your account';
    $page_content = '';

    /* Get error msg from POST route */
    if ( isset($_GET['error_msg']) ) {
        $error_msg = get_error($_GET['error_msg']);
    }

    /* Choose Template */
    include use_template('account');

}
/* register GET */
elseif (new_route('/DDWT18/week2/register/', 'get')) {
    /* Page info */
    $page_title = 'Register';
    $breadcrumbs = get_breadcrumbs([
        'DDWT18' => na('/DDWT18/', False),
        'Week 2' => na('/DDWT18/week2/', False),
        'Register' => na('/DDWT18/week2/register/', True)
    ]);
    $navigation = get_navigation($template, 4);
    /* Page content */
    $page_subtitle = 'Register on Series Overview!';
    /* Get error msg from POST route */
    if ( isset($_GET['error_msg']) ) { $error_msg = get_error($_GET['error_msg']); }
    /* Choose Template */
    include use_template('register');
}

/* register POST */
elseif (new_route('/DDWT18/week2/register/', 'post')) {
    /* Register user */
    $feedback = register_user($db, $_POST);
    /* Redirect to homepage */
    redirect(sprintf('/DDWT18/week2/register/?error_msg=%s',
        json_encode($feedback)));
}

/* login GET */
elseif (new_route('/DDWT18/week2/login/', 'get')) {
    /* Page info */

    /* Check if logged in */
    if ( check_login() ) {
        redirect('/DDWT18/week2/myaccount/');
    }
    $page_title = 'Login';
    $breadcrumbs = get_breadcrumbs([
        'DDWT18' => na('/DDWT18/', False),
        'Week 2' => na('/DDWT18/week2/', False),
        'Overview' => na('/DDWT18/week2/login', True)
    ]);
    $navigation = get_navigation($template, 5);
    /* Page content */
    $page_subtitle = 'Use your username and password to login';
    /* Get error msg from POST route */
    if ( isset($_GET['error_msg']) ) { $error_msg = get_error($_GET['error_msg']); }
    /* Choose Template */
    include use_template('login');

}
/* login POST */
elseif (new_route('/DDWT18/week2/login/', 'post')) {
    /* login user */
    $feedback = login_user($db, $_POST);

    /* Redirect to homepage */
    redirect(sprintf('/DDWT18/week2/login/?error_msg=%s',
        json_encode($feedback)));

}
/* logout GET */
elseif (new_route('/DDWT18/week2/logout/', 'get')) {
    $feedback = logout_user();

}
else {
    http_response_code(404);
}