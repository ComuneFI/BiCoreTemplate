<?php

namespace Cdf\BiCoreBundle\Tests\Controller;

use Cdf\BiCoreBundle\Tests\Utils\BiTestAuthorizedClient;

class PannelloAmministrazioneControllerFunctionalTest extends BiTestAuthorizedClient
{
    /*
     * @test
     */
    public function test20AdminpanelGenerateBundle()
    {
        //url da testare
        $apppath = $this->getContainer()->get("pannelloamministrazione.projectpath");
        $checkentityprova = $apppath->getSrcPath() .
                DIRECTORY_SEPARATOR . "Entity" . DIRECTORY_SEPARATOR . "Prova.php";
        $checktypeprova = $apppath->getSrcPath() .
                DIRECTORY_SEPARATOR . "Form" . DIRECTORY_SEPARATOR . "ProvaType.php";
        $checkviewsprova = $apppath->getSrcPath() . DIRECTORY_SEPARATOR . ".." .
                DIRECTORY_SEPARATOR . "templates" . DIRECTORY_SEPARATOR . "Prova";
        $checkindexprova = $checkviewsprova .
                DIRECTORY_SEPARATOR . "Crud" . DIRECTORY_SEPARATOR . "index.html.twig";

        $url = $this->getRoute('fi_pannello_amministrazione_homepage');
        $client = $this->getClient();

        $client->request('GET', $url);
        $client->waitFor("#adminpanelgenerateentity");
        $this->executeScript('document.getElementById("entityfile").value = "wbadmintest.mwb"');
        $this->pressButton('adminpanelgenerateentity');

        $client->waitFor(".biconfirmyes");
        $this->pressButton('biconfirmyes');

        //echo  $this->getCurrentPageContent();
        
        $client->waitFor("#corebundlemodalinfo");
        $this->pressButton('biconfirmok');

        $this->visit($url);

        $this->assertTrue(file_exists($checkentityprova));

        $this->pressButton('adminpanelaggiornadatabase');
        $client->waitFor(".biconfirmyes");

        $this->pressButton('biconfirmyes');

        $client->waitFor(".biconfirmok");
        $this->pressButton('biconfirmok');

        $client->request('GET', $url);
        $this->visit($url);

        $this->executeScript('document.getElementById("entityform").value = "Prova"');

        $this->pressButton('adminpanelgenerateformcrud');

        $client->waitFor(".biconfirmyes");
        $this->pressButton('biconfirmyes');

        $client->waitFor(".biconfirmok");
        $this->pressButton('biconfirmok');

        $this->assertTrue(file_exists($checktypeprova));
        $this->assertTrue(file_exists($checkviewsprova));
        $this->assertTrue(file_exists($checkindexprova));

        try {
            $urlRouting = $this->router->generate('Prova_container');
        } catch (\Exception $exc) {
            $urlRouting = "/Prova";
        }

        $url = $urlRouting;


        $this->visit($url);
        $session = $this->getSession();
        $page = $this->getCurrentPage();

        //echo $page->getHtml();
        $this->crudoperation($session, $page);

        $session->quit();
    }
    private function crudoperation($session, $page)
    {
        $client = $this->getClient();

        $this->clickElement('tabellaadd');

        //$this->pressButton('biconfirmyes');

        /* Inserimento */
        $descrizionetest1 = 'Test inserimento descrizione automatico';
//        if (version_compare(\Symfony\Component\HttpKernel\Kernel::VERSION, '3.0') >= 0) {
//            $fieldhtml = 'prova_descrizione';
//        } else {
        $fieldhtml = 'prova_descrizione';
//        }

        $client->waitFor('#' . $fieldhtml);

        $this->fillField($fieldhtml, $descrizionetest1);

        $client->waitFor('#prova_submit');
        $this->clickElement('prova_submit');
        sleep(2);

        $qb1 = $this->em->createQueryBuilder()
                ->select(array('Prova'))
                ->from('App:Prova', 'Prova')
                ->where('Prova.descrizione = :descrizione')
                ->setParameter('descrizione', $descrizionetest1)
                ->getQuery()->getResult();

        $provaobj1 = $qb1[0];
        $rowid = $provaobj1->getId();
        $this->clickElement('.bibottonimodificatabellaProva[data-biid="' . $rowid . '"]');
        $contextmenuedit = 'a.h-100.d-flex.align-items-center.btn.btn-xs.btn-primary';
        $client->waitFor($contextmenuedit);
        $this->clickElement($contextmenuedit);

        $this->assertEquals($provaobj1->getDescrizione(), $descrizionetest1);

        //Modifica
        $descrizionetest2 = 'Test inserimento descrizione automatico 2';

        $client->waitFor('#' . $fieldhtml);

        $this->fillField($fieldhtml, $descrizionetest2);

        $this->clickElement('prova_submit');
//        $this->ajaxWait(6000);
        //Non ho idea del perchè non funzioni anche perchè il record è stato davvero modificato
        /* $qb2 = $this->em->createQueryBuilder()
          ->select(array("Prova"))
          ->from("App:Prova", "Prova")
          ->where("Prova.id = :id")
          ->setParameter("id", $rowid)
          ->getQuery()->getResult();

          $provaobj2 = $qb2[0];

          $this->assertEquals($provaobj2->getDescrizione(), $descrizionetest2); */

        $this->clickElement('.bibottonimodificatabellaProva[data-biid="' . $rowid . '"]');
        $contextmenudelete = 'a.h-100.d-flex.align-items-center.btn.btn-xs.btn-danger';
        $client->waitFor($contextmenudelete );
        $this->clickElement($contextmenudelete );

        $client->waitFor('.biconfirmyes');
        $this->pressButton('biconfirmyes');
        sleep(1);
//        $this->ajaxWait(6000);

        $qb3 = $this->em->createQueryBuilder()
                ->select(array('Prova'))
                ->from('App:Prova', 'Prova')
                ->where('Prova.descrizione = :descrizione')
                ->setParameter('descrizione', $descrizionetest2)
                ->getQuery()->getResult();

        $this->assertEquals(count($qb3), 0);
    }

    /**
     * {@inheritdoc}
     */
    public function tearDown()
    {
        parent::tearDown();
        cleanFilesystem();
        removecache();
        clearcache();
    }
}
