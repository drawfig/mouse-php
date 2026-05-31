<! doctype html>
<html>
    <head>
        <meta charset="utf-8" />
        <meta name="viewport"
              content="width=device-width,
              initial-scale=1" />
        <?php
            echo $this->format_styles();
            echo $this->format_scripts();
            echo $this->get_favicon();
        ?>

        <title>
            <?php echo $this->title_format($this->VIEW_NAME); ?>
        </title>
    </head>
    <body>
        <?php
            echo $this->view_render($this->VIEW_NAME);
        ?>
    </body>
</html>