<?php

require "Db.php";
require "Main.php";

$api = new Main();
$db = new Db();

$users = $db->getUsers();

$analyzed = false;

if (isset($_GET['username'])) {
  $id = $_GET['username'];
  $user = $api->getUser($id);
  $postParseCount = 50000;
  $username = $user['first_name'] . " " . $user['last_name'];
  $db->registerUser($user);
  $counter = 0;
  for ($i = 0; $i <= $postParseCount; $i = $i + 100) {
    $analyzed = true;
    $posts = $api->getWallPosts($user['id'], $i);
    if (empty($posts) || is_null($posts)) break;
    $counter += $db->registerPosts($posts);
  }
}

if (isset($_GET['userID'])) {
	$from = strtotime($_GET['from']);
	$to = strtotime($_GET['to']);
	if ($from > $to) {
		echo "It's not true;"; exit();
	}
	$user = $db->getUser($_GET['userID']);

  $posts = $db->getUserPosts($_GET['userID'], $from, $to);
	foreach ($posts as $post) {
      $counts[] = count($post);
      $dates[] = date("d.m.Y", $post[0]['posted']);
  }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="Mark Otto, Jacob Thornton, and Bootstrap contributors">
  <meta name="generator" content="Jekyll v4.1.1">
  <title>Vk user posts analyzer</title>

  <!-- Bootstrap core CSS -->
  <link href="dist/bs/css/bootstrap.min.css" rel="stylesheet">
  <link href="dist/custom/css/custom.css" rel="stylesheet">
  <link href="dist/datepicker/css/bootstrap-datepicker.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
  <script src="https://cdn.jsdelivr.net/npm/echarts@4.9.0/dist/echarts.min.js" type="text/javascript"></script>
</head>
<body>
<div class="d-flex flex-column flex-md-row align-items-center p-3 px-md-4 mb-3 bg-white border-bottom shadow-sm">
  <h5 class="my-0 mr-md-auto font-weight-normal">Company name</h5>
  <nav class="my-2 my-md-0 mr-md-3">
    <a class="p-2 text-dark" href="#">Features</a>
    <a class="p-2 text-dark" href="#">Enterprise</a>
    <a class="p-2 text-dark" href="#">Support</a>
    <a class="p-2 text-dark" href="#">Pricing</a>
  </nav>
  <a class="btn btn-outline-primary" href="#">Sign up</a>
</div>

<div class="container">
  <div class="col-md-12">
    <h4 class="mb-3">Vk analyzer</h4>
    <form class="needs-validation" novalidate="">
      <div class="mb-3">
        <label for="username">Username</label>
        <div class="input-group">
          <div class="input-group-prepend">
            <span class="input-group-text">@</span>
          </div>
          <input type="text" class="form-control" id="username" placeholder="Username" name="username" required="">
        </div>
      </div>
      <hr class="mb-4">
      <button class="btn btn-primary btn-lg btn-block" type="submit">Get user posts!</button>
      <hr class="mb-4">
    </form>
  </div>
</div>
<div class="container">
  <div class="col-md-12">
    <form class="needs-validation">
      <div class="row">
        <div class="col-md-12 mb-3">
          <label for="country">Select an user</label>
          <select class="custom-select d-block w-100" id="username" required="" name="userID">
            <option value="">Choose...</option>
            <?php foreach ($users as $user): ?>
              <option value="<?= $user[2] ?>"><?= $user[1] ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="span5 col-md-5" id="sandbox-container"><input placeholder="from" name="from" type="text" class="form-control"></div>
        <div class="span5 col-md-5" id="sandbox-container"><input placeholder="to" name="to" type="text" class="form-control"></div>
      <hr class="mb-4">
      <button class="btn btn-primary btn-lg btn-block" type="submit">Show analyze data!</button>
      <hr class="mb-4">
    </form>
  </div>
</div>

<div class="container">
  <div class="card-deck mb-3 text-center">
    <div class="col-md shadow-sm">
      <div class="card-header">
        <h4 class="my-0 font-weight-normal">User posts diagram</h4>
      </div>
      <div class="card-body">
        <?php if (isset($_GET['userID'])): ?>
          <div id="main" style="width:1000px; height:400px;"></div>
        <?php else: ?>
          <div class="">Select user for analyze</div>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <footer class="pt-4 my-md-5 pt-md-5 border-top">
    <div class="row">
      <div class="col-12 col-md">
        <small class="d-block mb-3 text-muted">&copy; 2020</small>
      </div>
      <div class="col-6 col-md">
        <h5>Features</h5>
        <ul class="list-unstyled text-small">
          <li><a class="text-muted" href="#">Cool stuff</a></li>
          <li><a class="text-muted" href="#">Random feature</a></li>
          <li><a class="text-muted" href="#">Team feature</a></li>
          <li><a class="text-muted" href="#">Stuff for developers</a></li>
          <li><a class="text-muted" href="#">Another one</a></li>
          <li><a class="text-muted" href="#">Last time</a></li>
        </ul>
      </div>
      <div class="col-6 col-md">
        <h5>Resources</h5>
        <ul class="list-unstyled text-small">
          <li><a class="text-muted" href="#">Resource</a></li>
          <li><a class="text-muted" href="#">Resource name</a></li>
          <li><a class="text-muted" href="#">Another resource</a></li>
          <li><a class="text-muted" href="#">Final resource</a></li>
        </ul>
      </div>
      <div class="col-6 col-md">
        <h5>About</h5>
        <ul class="list-unstyled text-small">
          <li><a class="text-muted" href="#">Team</a></li>
          <li><a class="text-muted" href="#">Locations</a></li>
          <li><a class="text-muted" href="#">Privacy</a></li>
          <li><a class="text-muted" href="#">Terms</a></li>
        </ul>
      </div>
    </div>
  </footer>
</div>
<script src="dist/custom/js/jquery-3.5.1.min.js"></script>
<script src="dist/bs/js/bootstrap.min.js"></script>
<script src="dist/datepicker/js/bootstrap-datepicker.min.js"></script>
<script type="text/javascript">
	$('#sandbox-container input').datepicker({});
</script>
<?php if (isset($_GET['userID'])) : ?>
<script type="text/javascript">
  // based on prepared DOM, initialize echarts instance
  var myChart = echarts.init(document.getElementById('main'));

  // specify chart configuration item and data
  var option = {
    title: {
      text: 'User posts data'
    },
    tooltip: {},
    xAxis: {
      data: <?= json_encode($dates) ?>
    },
    yAxis: {},
    series: [{
      type: 'bar',
      data: <?= json_encode($counts) ?>
    }]
  };

  // use configuration item and data specified to show chart
  myChart.setOption(option);
</script>
<?php endif; ?>

<?php if ($analyzed): ?>
  <script>
    Swal.fire({
      title: '<?= $username ?> was analyzed!',
      text: '<?= $counter ?> posts are saved to databse',
      icon: 'success',
      confirmButtonText: 'Close'
    })
  </script>
<?php endif; ?>
</body>
</html>
