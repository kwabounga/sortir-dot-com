<?php


namespace App\Services;


use App\Entity\Campus;
use App\Entity\Etat;
use App\Entity\Role;
use App\Entity\User;
use App\Entity\Ville;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class InitialisationService
{
    public static function firstInitBdd(EntityManagerInterface $em, UserPasswordEncoderInterface $encoder, $scriptDir) {
        InitialisationService::initRoles($em);
        InitialisationService::initCampus($em);
        InitialisationService::initVilles($em);
        InitialisationService::initEtats($em);
        InitialisationService::initAdmin($em, $encoder);
        InitialisationService::initSQL($em,  $scriptDir);
    }

    private static function initAdmin(EntityManagerInterface $em, UserPasswordEncoderInterface $encoder){
        $login = 'admin';
        $passw = 'admin';
        try {
            $user = new User();
            $user->setDateCreated(new \DateTime());
            $user->setUsername($login);
            $user->setCampus($em->getRepository(Campus::class)->find(1));
            $user->setRole($em->getRepository(Role::class)->find(1));
            $user->setMail('admin@admin.fr');
            $user->setPhone('0240252525');
            $user->setFirstname('administrateur');
            $user->setLastname('de l\'eni');

            $hash = $encoder->encodePassword($user, $passw);
            $user->setPassword($hash);
            $em->persist($user);
            $em->flush();
        } catch (\Exception $e) {
            dump($e);
        }

    }
//    private static function initSQL(EntityManagerInterface $em, $kernel, $scriptDir){
//        $application = new \Symfony\Bundle\FrameworkBundle\Console\Application($kernel);
//        $application->setAutoExit(false);
////        //Create de BDD
////        $options = array('command' => 'doctrine:database:create');
////        $application->run(new \Symfony\Component\Console\Input\ArrayInput($options));
//        //Create de Schema
//        $options = array('command' => 'doctrine:schema:update',"--force" => true);
//        $application->run(new \Symfony\Component\Console\Input\ArrayInput($options));
//
//    }
    private static function initSQL(EntityManagerInterface $em,  $scriptDir){
        $script = $scriptDir;
        dump($script);
        $buffer = null;
        if (file_exists($script)) {
            if(false !== $handle = @fopen($script, 'r')) {
                $buffer = [];
                while (($word = fgets($handle)) !== false) {
                    $buffer[] = $word;
                }
                fclose($handle);
            } else {
                // erreur
            }
        } else {
            $buffer = ['nothing here'];
        }
        dump($buffer);

        $conn = $em->getConnection();
        $sql = join(" ", $buffer);
        $stmt = $conn->prepare($sql);
        $stmt->execute();
    }
    private static function initRoles(EntityManagerInterface $em) {
        $roles = $em->getRepository(Role::class)->findAll();
        if(count($roles) === 0 ){
            $r1 = new Role();
            $r1->setValue('ROLE_ADMIN');
            $r2 = new Role();
            $r2->setValue('ROLE_USER');
            $em->persist($r1);
            $em->persist($r2);
            $em->flush();
        } else {
            // roles deja initialisés
        }
    }
    private static function initCampus(EntityManagerInterface $em) {
        $campus = $em->getRepository(Campus::class)->findAll();
        if(count($campus) === 0 ){
            $cp = ['Nantes', 'Renne', 'Niord'];
            for ($i = 0 ; $i<count($cp); $i++){
                $c = new Campus();
                $c->setNom($cp[$i]);
                $em->persist($c);
            }
            $em->flush();
        } else {
            // Campus deja initialisés
        }
    }
    private static function initVilles(EntityManagerInterface $em) {
        $villes = $em->getRepository(Ville::class)->findAll();
        if(count($villes) === 0 ){
            $vs = [
                ['CHAUMES EN RETZ',44320,47.1592162214,-1.95412512421],
                ['AVESSAC',44460,47.635436304,-1.97050871505],
                ['BESNE',44160,47.3800629578,-2.07409913989],
                ['LE BIGNON',44140,47.1004098153,-1.50942309254],
                ['LA BOISSIERE DU DORE',44430,47.2350761826,-1.20383839178],
                ['BOUGUENAIS',44340,47.1709061678,-1.61739752858],
                ['VILLENEUVE EN RETZ',44580,47.0381472853,-1.92586734325],
                ['LE CELLIER',44850,47.3374810886,-1.35950738339],
                ['LA CHAPELLE GLAIN',44670,47.6202560031,-1.19540234544],
                ['CHAUVE',44320,47.1588661346,-2.01200123653],
                ['COUERON',44220,47.2309985029,-1.72929002573],
                ['FAY DE BRETAGNE',44130,47.3905699563,-1.79530261946],
                ['GETIGNE',44190,47.0812318041,-1.21795705627],
                ['GUENROUET',44530,47.5034137503,-1.95561601434],
                ['ISSE',44520,47.6173601577,-1.4560368491],
                ['LA LIMOUZINIERE',44310,46.9872257466,-1.64166009462],
                ['MACHECOUL ST MEME',44270,46.9910784513,-1.82345670327],
                ['MALVILLE',44260,47.3431404212,-1.85411536756],
                ['LES MOUTIERS EN RETZ',44760,47.0616219129,-1.99001867615],
                ['NANTES',44000,47.2316356767,-1.54831008605],
                ['NOZAY',44170,47.5728858483,-1.60097664616],
                ['PAULX',44270,46.95778792,-1.77844057969],
                ['LE PELLERIN',44640,47.2229017049,-1.8250130027],
                ['PORNIC',44210,47.1223972452,-2.05182334479],
                ['ROUANS',44640,47.1763592414,-1.84313535499],
                ['ST FIACRE SUR MAINE',44690,47.1431877393,-1.41565837757],
                ['ST LUMINE DE CLISSON',44190,47.0796411393,-1.36298171094],
                ['ST MALO DE GUERSAC',44550,47.3561985163,-2.17203654541],
                ['VALLONS DE L ERDRE',44540,47.5309227299,-1.19441075506],
                ['ST NAZAIRE',44600,47.2802857028,-2.25379927249],
                ['ST PHILBERT DE GRAND LIEU',44310,47.0602912538,-1.65711361304],
                ['SION LES MINES',44590,47.7291886842,-1.58109954157],
                ['LES SORINIERES',44840,47.1414647152,-1.5202224711],
                ['THOUARE SUR LOIRE',44470,47.2763256866,-1.43162198248],
                ['LES TOUCHES',44390,47.4523563976,-1.42756883238],
                ['TRIGNAC',44570,47.3125328416,-2.20700045585],
                ['VUE',44640,47.1941974605,-1.9008768965],
                ['LA CHEVALLERAIS',44810,47.477183891,-1.66493224738],
                ['ANDONVILLE',45480,48.2740984762,2.02318881395],
                ['ASCHERES LE MARCHE',45170,48.1120145126,2.01033081704],
                ['AUTRY LE CHATEL',45500,47.5949029716,2.60209574772],
                ['AUVILLIERS EN GATINAIS',45270,47.9653555404,2.49120445064],
                ['BAULE',45130,47.8106703162,1.66824027351],
                ['BOISMORAND',45290,47.7798249576,2.72028920172],
                ['BONNEE',45460,47.7942659998,2.39324729287],
                ['LES BORDES',45460,47.8163497112,2.43671034937],
                ['BOU',45430,47.8704664522,2.04729734716],
                ['BOUILLY EN GATINAIS',45300,48.0981078999,2.28756909213],
                ['BOUZONVILLE AUX BOIS',45300,48.1043167535,2.2461940499],
                ['BRIARE',45250,47.6450008152,2.74519258713],
                ['CERDON',45620,47.6299186758,2.38147298677],
                ['CHAILLY EN GATINAIS',45260,47.936543644,2.5277446897],
                ['CHAMBON LA FORET',45340,48.0490368743,2.28348190868],
                ['LA CHAPELLE SUR AVEYRON',45230,47.8707521485,2.87448096563],
                ['CHARMONT EN BEAUCE',45480,48.2376605763,2.11465872922],
                ['CHATEAUNEUF SUR LOIRE',45110,47.8851472101,2.23195371871],
                ['CHEVANNES',45210,48.1314865857,2.85958457085],
                ['CHEVILLON SUR HUILLARD',45700,47.9668347404,2.62231250927],
                ['CHILLEURS AUX BOIS',45170,48.0570941701,2.14189355578],
                ['COURCELLES LE ROI',45300,48.0980883547,2.32759731775],
                ['CROTTES EN PITHIVERAIS',45170,48.1141192289,2.06490558208],
            ];
            for ($i = 0 ; $i<(count($vs)-1); $i++){
                $v = new Ville();
                $v->setNom($vs[$i][0]);
                $v->setCodePostal($vs[$i][1]);
                $v->setLatitude($vs[$i][2]);
                $v->setLongitude($vs[$i][3]);
                $em->persist($v);
            }
            $em->flush();
        } else {
            // villes deja initialisés
        }
    }
    private static function initEtats(EntityManagerInterface $em) {
        $states = $em->getRepository(Etat::class)->findAll();
        if(count($states) === 0 ){
            $as = ['en creation', 'ouverte', 'clôturée', 'en cours', 'terminée', 'historisée'];
            for ($i = 0 ; $i<count($as); $i++){
                $r = new Etat();
                $r->setLibelle($as[$i]);
                $em->persist($r);
            }
            $em->flush();
        } else {
            // etats deja initialisés
        }
    }
}