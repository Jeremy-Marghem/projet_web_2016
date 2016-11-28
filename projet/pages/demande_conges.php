<br/><br/><br/><br/>
<div class="bootstrap-iso">
    <div class="container">
        <div class="row">
            <div class="col-lg-offset-4 col-lg-4 col-md-offset-4 col-md-4 col-sm-offset-3 col-sm-6 col-xs-offset-2 col-xs-8">
                <form method="post" action="<?php $_SERVER['PHP_SELF']; ?>">
                    <div class="form-group">
                        <label class="control-label" for="date_debut">Date de début</label>
                        <input class="form-control" id="date_debut" name="date_debut" placeholder="JJ MM AAAA" type="text"/>
                    </div>
                    <div class="form-group">
                        <label class="control-label" for="date_fin">Date de fin</label>
                        <input class="form-control" id="date_fin" name="date_fin" placeholder="JJ MM AAAA" type="text"/>
                    </div>
                    <br/>
                    <div>
                        <label>Jours de congés disponible(s): <?php echo $_SESSION['conges'] ?></label>
                    </div>
                    <br/><br/>
                    <div class="form-group">
                        <div>
                            <input name="_honey" style="display:none" type="text"/>
                            <button class="btn btn-primary" name="submit" type="submit">Envoyer la demande</button>
                        </div>
                    </div>
                    <div>
                        <span id="remarque"></span>
                    </div>
                </form>
            </div>
            <script>
                $(document).ready(function () {
                    var date_input = $('input[name="date_debut"]');
                    var container = $('.bootstrap-iso form').length > 0 ? $('.bootstrap-iso form').parent() : "body";
                    var options = {
                        format: 'dd M yyyy',
                        container: container,
                        todayHighlight: true,
                        autoclose: true,
                        weekStart: 1,
                        daysOfWeekDisabled: "0,6"
                    };
                    date_input.datepicker(options);
                });
                $(document).ready(function () {
                    var date_input = $('input[name="date_fin"]');
                    var container = $('.bootstrap-iso form').length > 0 ? $('.bootstrap-iso form').parent() : "body";
                    var options = {
                        format: 'dd M yyyy',
                        container: container,
                        todayHighlight: true,
                        autoclose: true,
                        weekStart: 1,
                        daysOfWeekDisabled: "0,6"
                    };
                    date_input.datepicker(options);
                });
            </script>
        </div> 
    </div>
</div>
<?php
if (isset($_POST['submit'])) {
    $debut = date_create($_POST['date_debut']);
    $fin = date_create($_POST['date_fin']);

    //On recupere les numeros de semaines ain de pouvoir en deduire le nombre de week-end compris
    $semaineDebut = new DateTime($_POST['date_debut']);
    $semaineDebut = $semaineDebut->format('W');
    $semaineFin = new DateTime($_POST['date_fin']);
    $semaineFin = $semaineFin->format('W');

    //Nombre de week-ends
    $nbWeekEnds = $semaineFin - $semaineDebut;
    $weekEnds = $nbWeekEnds * 2;

    //Calcul du nombre de jours
    $interval = $debut->diff($fin);
    $interval = $interval->format('%a');
    $interval = intval($interval);

    //On retire du nombre de jours le nombre de jours compris lors des weeks ends
    $interval = $interval - $weekEnds;

    if ($interval > $_SESSION['conges'] - 1) {
        //SI CONGES TROP LONG
        ?>
        <script>
            $('#remarque').html("Vous ne disposez que de " +<?php echo $_SESSION['conges'] ?> + " jour(s) de congés");
        </script>        
        <?php
    } else {
        //SI DEMANDE CORRECTE

        $info = new InfoCongesDB($cnx);
        $resultat = $info->createConge($_POST['date_debut'], $_POST['date_fin'], $interval + 1, $_SESSION['id']);

        if ($resultat == 1) {
            ?>
            <script>
                $('#remarque').html("Demande enregistrée!");
            </script>    
            <?php
        }
    }
}

function transform($string) {
    $annee = 0;
    $annee = substr($string, 0, 4);
    $mois = 0;
    $mois = substr($string, 5, 2);
    switch ($mois) {
        case 1: $mois = "Janvier";
            break;
        case 2: $mois = "Fevrier";
            break;
        case 3: $mois = "Mars";
            break;
        case 4: $mois = "Avril";
            break;
        case 5: $mois = "Mai";
            break;
        case 6: $mois = "Juin";
            break;
        case 7: $mois = "Juillet";
            break;
        case 8: $mois = "Aout";
            break;
        case 9: $mois = "Septembre";
            break;
        case 10: $mois = "Octobre";
            break;
        case 11: $mois = "Novembre";
            break;
        case 12: $mois = "Decembre";
            break;
    }
    $jour = 0;
    $jour = substr($string, 8, 2);
    return $jour . " " . $mois . " " . $annee;
}
?>