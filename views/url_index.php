<?php
// if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
//   http_response_code(405);
//   header('allow: GET, HEAD');
//   echo '<h1>405 Method Not Allowed</h1>';
//   exit();
// }
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1, shrink-to-fit=no"
    />

    <!-- Bootstrap CSS -->
    <link
      rel="stylesheet"
      href="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css"
      integrity="sha384-GJzZqFGwb1QTTN6wy59ffF1BuGJpLSa9DkKMp0DgiMDm4iYMj70gZWKYbI706tWS"
      crossorigin="anonymous"
    />
    <title>TinyApp</title>
  </head>
  <body>
    <?php include "./includes/_header.html" ?>
    <main style="margin: 1em;">
      <h3>My URLs</h3>
      <p> Length = <?php echo count($urls) ?> </p>
      <table class="table">
        <thead>
          <tr>
            <th scope="col">Short URL ID</th>
            <th scope="col">Long URL</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($urls as $index => $url) { ?>
            <tr>
              <td><?php echo $url["short_url"] ?></td>
              <td><?php echo $url["long_url"] ?></td>
            </tr>
          <?php } ?>
        </tbody>
      </table>
    </main>
    <!-- Bootstrap JS -->
    <script
      src="https://code.jquery.com/jquery-3.3.1.slim.min.js"
      integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo"
      crossorigin="anonymous"
    ></script>
    <script
      src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"
      integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1"
      crossorigin="anonymous"
    ></script>
    <script
      src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"
      integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM"
      crossorigin="anonymous"
    ></script>
  </body>
</html>