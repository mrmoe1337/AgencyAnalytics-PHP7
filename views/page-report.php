<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>AgencyAnalytics Crawler</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
</head>
<body>

<div class="container">
    <div class="row">
        <div class="mt-5 col-md-12">
            <h1>AgencyAnalytics Crawler</h1>
            <hr/>
            <b>Pages Crawled:</b> <?= $pages ?><br/>
            <b>Unique images:</b> <?= $imgStorage ?><br/>
            <b>Unique Internal Links:</b> <?=$urlStorageInternal?><br/>
            <b>Unique External Links:</b> <?=urlStorageExternal?><br/>
            <b>Average Load Time:</b> <?=$avgLoadTime?> ms<br/>
            <b>Average Word Count:</b> <?=$avgWordCount?><br/>
            <b>Average Title Length:</b> <?=$avgTitleLength?>

            <hr/>
            <h4>Crawled Pages</h4>
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                    <tr>
                        <td>URL</td>
                        <td>HTTP Status</td>
                        <td>Load Time</td>
                    </tr>
                    </thead>
                    <?php
                    foreach ($httpStatusStorage as $item) {
                        echo "<tr><td>" . $item['url'] . "</td> <td>" . $item['status'] . "</td><td>".$item['loadTime']."ms</td></tr>";
                    }
                    ?>
                </table>
            </div>
        </div>
    </div>
</div>

</body>
</html>