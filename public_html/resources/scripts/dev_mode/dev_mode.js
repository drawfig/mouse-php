let logo = "                                        &&&&               &&$$X&                                    \n" +
    "                                     &$$XX$$$$&           &&$$+:+&                                   \n" +
    "                                   &$+:::;++x$X$&     && &&$$$x::;&                                  \n" +
    "                                  &$:::::::;++&X$& &&$&&&&&$$$&;::X&                                 \n" +
    "                                  &;::::::::;+XXX$$XXXXXX$$$$$&;::x&                                 \n" +
    "                                  &;:::::::;;++$XXXXXXXXX$$XXXX::;$                                  \n" +
    "                                   $:::::::+x$x$XXXXXXXXXXXXXXX$:x&                                  \n" +
    "                                   &$::::::;++&XXXX$$XXXXXXXXXXX&&                                   \n" +
    "                                     &x:::::::XXXXXXXXXXXXXXXXXX&&                                   \n" +
    "                                       &&$XX$$XXXXX$;$&XXXXXXX:$$&                                   \n" +
    "                                            &$XXXX$&&&&$XXXXXX&&X&&&&&&                              \n" +
    "                       &&&&&&              &$$XXXXX$&&$XX+:::;+X$$&&&&                               \n" +
    "                      &&    &$             &&$XXXXXXX$$X+::;X+:xX&&&                                 \n" +
    "                            &X&             &&$$$$XXX$XXXxXxX$$$&                                    \n" +
    "                            &$          &&$$$X$$&$$$XXXXXXX$$&&                                      \n" +
    "                           &X&        &$$XXXXXXXXX$$+;;;X                                            \n" +
    "                          &X&       &&XXXXXXXXXXXXX+::::&&                                           \n" +
    "                         &X&       &&$XXXXXXXXX$XXXX;::X$$&&                                         \n" +
    "                         &+&       &$XXXXXXXX$X$$$XX$xX&&$+:+xxxxxxx$&&&$$&&                         \n" +
    "                         &XX&     &&$$XXXXXXX&;:;+XXX;:X$Xx+x++++++;;;+XXXXX$&                       \n" +
    "                          &X+X&&&&$$$$$XXXXXXX:Xxxxxxxx$+++++++++++++++;+$$$$&&                      \n" +
    "                            &XXXXX$$&&$$$$$$$Xx++++++++++++++++++++++++++;x$$$&                      \n" +
    "                                  &&$XX$Xx++$++++XXXX$$+++++++++++++++++++;x$$&                      \n" +
    "                                &X++++xXXXX$x++xX+;:;+xXX++++++Xx++++++++++;X&&                      \n" +
    "                              &$++++++++++Xx+++:.......:+x+++++++++++++++++;+&                       \n" +
    "                             &X+++++++++++Xx+:..........:++++++++++++++++++++$                       \n" +
    "                            &$++++++++++++X+:...........:++++++$&x+++++++++++$                       \n" +
    "                            &x+++++++;.  :XX+::.........:X++++$+&&x++++++++++$                       \n" +
    "                           &$+++++;   :+++++x:X$;.......:X++++x$&X++++++x$$Xx&                       \n" +
    "                           &Xx++++;  .;+++++  +X$:......Xx+++++++++++++++++xX&                       \n" +
    "                           &Xx+++++++;   ;+; ;+x$+:::::x$x+++++++++++++++++x$&                       \n" +
    "                           &$x++++++++++;;+ :+++x$XxxX$X$Xxx++++xXX+++++xx++$                        \n" +
    "                           &$$x+++++++++++. ;+++++xxxxxxXX$$XXXxxxX$x++++xxxX&                       \n" +
    "                          &&&&X++++++++++++++++++++++++++xXXXX$&&$&&$x++++xxx&&&$$$&                 \n" +
    "                        &&&& &$x++++++++++++++++++++++++++xXXX$XXX& &$x++++++xXXx+xX&                \n" +
    "                       &&&&   &X++++++++xXxxxxxxxx++++++++$$$XXXXX&  &&$x+++++++x++X&                \n" +
    "                      &&&&    &$x++++++xX$$$$$XX$$x+++++xXXXXXXXXX&    &&$$XxxxX$&&&                 \n" +
    "                              &$x++++XXXXXXXXXX&&$x+++++xXXXXXXXXX&        &&&&&                     \n" +
    "                              &$x++++++xXXXxXx+&&$x+++++++x$XXXXX$$&                                 \n" +
    "                        &&&&&&&&Xxx. .+;&&$X$$&&&$Xx++:.;::&$$xxX$&&&&&&&&&&&&&&                     \n" +
    "                          &&&&&&&&&&&&&&&&&&&&&&&&&&&&$X$$&&&&&&&&&&&&&&&&&&&&                       \n" +
    "                                                                                                     \n" +
    "                                                                                                     \n" +
    "                                                                                                     \n" +
    "                                                                              XXXX                   \n" +
    "                                                                        X     XXXXX                  \n" +
    "XXXXXXXXXXXXXXXX  XXXXXXXXX  XXX   XXXX XXXXXXXX  XXXXXXXXX        XXXXXXXXXX XXXXXXXXXXX XXXXXXXXXXX\n" +
    "XXXX XXXXX  XXXX XXXX  XXXXX XXX   XXXXXXXXXXX   XXXXXXXXXX XXXXXX XXXX   XXXXXXXXX  XXXX XXXXX  XXXX\n" +
    "XXX   XXXX  XXXX XXXX   XXXX XXX   XXXX XXXXXXXXXXXXXXXXXXX XXXXXX XXXX   XXXXXXXX   XXXXXXXXX   XXXX\n" +
    "XXX   XXXX  XXXX XXXXXXXXXX  XXXXXXXXXX XXXXXXXXXXXXXXXXXX         XXXXXXXXXXXXXXX   XXXXXXXXXXXXXXXX\n" +
    "XXX   XXX   XXXX   XXXXXXX    XXXXXXXXX XXXXXXXX  XXXXXXXX         XXXXXXXXX  XXXX   XXXX XXXXXXXXXX \n" +
    "                                                                   XXXX                   XXXX       \n";

console.log(logo);
console.log("________________________________________________________");
console.log("mouse-php is running in development mode");

const evtSource = new EventSource("/resources/scripts/dev_mode/change_event.php");

evtSource.onmessage = function(e) {
    location.reload();
};

evtSource.onerror = function(e) {
    console.log(e);
};

