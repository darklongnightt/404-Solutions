<ul class="pagination center">
    <?php if ($currPage == 1) { ?>
        <li class="disabled"><a href="#!"><i class="material-icons">chevron_left</i></a></li>
    <?php } else {
        $toPage = $currPage - 1; ?>
        <li class="waves-effect">
            <a href="<?php echo $currDir . '?page=' . $toPage . '&' . $_SESSION['j']; ?>">
                <i class="material-icons">chevron_left</i></a>
        </li>
    <?php } ?>

    <?php for ($page = 1; $page <= $totalPages; $page++) {
        if ($page == $currPage) { ?>
            <li class="active">
                <a href="<?php echo $currDir . '?page=' . $page . $ext; ?>"> <?php echo $page; ?> </a>
            </li>
        <?php } else { ?>
            <li class="waves-effect">
                <a href="<?php echo $currDir . '?page=' . $page . $ext; ?>"> <?php echo $page; ?> </a>
            </li>
    <?php }
    } ?>

    <?php if ($currPage == $totalPages) { ?>
        <li class="disabled"><a href="#!"><i class="material-icons">chevron_right</i></a></li>
    <?php } else {
        $toPage = $currPage + 1;
        ?>
        <li class="waves-effect">
            <a href="<?php echo $currDir . '?page=' . $toPage . '&' . $_SESSION['j']; ?>">
                <i class="material-icons">chevron_right</i></a>
        </li>
    <?php } ?>
</ul>