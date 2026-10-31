<?php
function create_alert($type, $pesan, $header = null)
{
    $_SESSION['adm-type'] = $type;
    $_SESSION['adm-message'] = $pesan;

    if ($header !== null) {
        header("Location: " . $header);
        exit();
    }
}

function show_alert()
{
    if (isset($_SESSION['adm-type'])) {
        $type = htmlspecialchars(ucfirst($_SESSION['adm-type']), ENT_QUOTES, 'UTF-8');
        $message = htmlspecialchars($_SESSION['adm-message'], ENT_QUOTES, 'UTF-8');
        unset($_SESSION['adm-type'], $_SESSION['adm-message']);

        return "<div class='alert alert-$type'>
            <strong>$type</strong><br>
            $message
        </div>";
    }

    return '';
}
