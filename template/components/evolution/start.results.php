<div class="w-full h-full bg-black bg-opacity-80 backdrop-blur-lg absolute left-0 top-0 flex flex-col bg-contain bg-center bg-no-repeat" id="result">
    <div class="w-full h-full flex items-end justify-center p-5">
        <?php if ($config['video']['enabled']): ?>
            <?php if ($config['video']['qr']): ?>
                <img src="" id="resultVideoQR" alt="video qr code">
            <?php endif; ?>
            <?php if ($config['video']['gif']) { ?>
                <img id="resultVideo" src="" alt="result gif">
            <?php } else { ?>
                <video id="resultVideo" autoplay loop muted>
                </video>
            <?php } ?>
        <?php endif; ?>

        <?php
            if (!$config['button']['force_buzzer']) {
                if ($config['picture']['enabled']) {
                    echo getBoothButton("newPhoto", $config['icons']['take_picture'], "newpic");
                }

                if ($config['custom']['enabled']) {
                    echo getBoothButton("newCustom", $config['icons']['take_custom'], "newcustom");
                }

                if ($config['collage']['enabled']) {
                    echo getBoothButton("newCollage", $config['icons']['take_collage'], "newcollage");
                }

                if ($config['video']['enabled']) {
                    echo getBoothButton("newVideo", $config['icons']['take_video'], "newvideo");
                }
            }
            if ($config['button']['homescreen']) {
                echo getBoothButton("home", $config['icons']['home'], "homebtn");
            }
            if($config['qr']['enabled']) {
                echo getBoothButton("qr", $config['icons']['qr'], "qrbtn");
            }
            if($config['gallery']['enabled']) {
                echo getBoothButton("gallery", $config['icons']['gallery'], "gallerybtn");
            }
            if($config['mail']['enabled']) {
                echo getBoothButton("mail", $config['icons']['mail'], "mailbtn");
            }
            if($config['print']['from_result']) {
                echo getBoothButton("print", $config['icons']['print'], "printbtn");
            }
            if($config['filters']['enabled']) {
                echo getBoothButton("selectFilter", $config['icons']['filter'], "imageFilter");
            }
            if($config['picture']['allow_delete']) {
                echo getBoothButton("delete", $config['icons']['delete'], "deletebtn");
            }
        ?>
    </div>

</div>
