<! doctype html>
<html>
    <head>
        <meta charset="utf-8" />
        <meta name="viewport"
              content="width=device-width,
              initial-scale=1" />
        <link rel="stylesheet"
              href="resources/styles/error.css" />
        <?php
            echo $this->format_scripts();
            echo $this->get_favicon();
        ?>

        <title>
            <?php echo $this->error_title_format($this->VIEW_NAME); ?>
        </title>
    </head>
    <body>
        <?php
            echo $this->error_render($this->VIEW_NAME);
        ?>
    </body>
</html>