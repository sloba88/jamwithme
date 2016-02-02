<?php

namespace Jam\WebBundle\Tests;

use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Behat\MinkExtension\Context\MinkContext;
use Behat\Testwork\Hook\Scope\BeforeSuiteScope;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\CssSelector\CssSelector;
use Behat\Mink\Exception\ElementNotFoundException;

/**
 * Defines application features from the specific context.
 */
class FeatureContext extends MinkContext implements Context, SnippetAcceptingContext
{

    protected $testUsername;

    protected $testPassword;

    /**
     * Initializes context.
     *
     * Every scenario gets its own context instance.
     * You can also pass arbitrary arguments to the
     * context constructor through behat.yml.
     */
    public function __construct($testUsername, $testPassword)
    {
        $this->testUsername = $testUsername;
        $this->testPassword = $testPassword;
    }

    protected static function runConsole($app, $command, Array $options = array())
    {
        $output = new ConsoleOutput();
        $output->writeln(sprintf('<comment>    > Command </comment> <info><fg=blue>%s</fg=blue></info>', $command));

        $options['-e'] = 'test';
        $options['-v'] = '3';
        //$options["-q"] = null;
        $options = array('command' => $command) + $options;

        return $app->run(new \Symfony\Component\Console\Input\ArrayInput($options), $output);
    }

    /** @BeforeSuite */
    public static function prepareForTheFeature(BeforeSuiteScope $scope)
    {
        $kernel = new \AppKernel('test', true);
        $kernel->boot();

        $app = new \Symfony\Bundle\FrameworkBundle\Console\Application($kernel);
        $app->setAutoExit(false);

        if( function_exists('opcache_reset') ) {
            opcache_reset();
        }

        //self::runConsole($app, 'cache:clear');

        self::runConsole($app, 'doctrine:database:drop', array('--force' => true));
        self::runConsole($app, 'doctrine:database:create');

        //Make sure we close the original connection because it lost the reference to the database
        $connection = $kernel->getContainer()->get('doctrine')->getConnection();
        if ($connection->isConnected()) {
            $connection->close();
        }
        self::runConsole($app, 'doctrine:schema:update', array('--force' => true));
        self::runConsole($app, 'doctrine:fixtures:load', array('--append' => true));
        self::runConsole($app, 'fos:elastica:populate');
    }

    /** @AfterStep */
    public function afterStep()
    {
        $this->iWaitForAjaxToFinish();
    }

    public function iWaitForAjaxToFinish()
    {
        $this->getSession()->wait(3000, '(typeof jQuery !== "undefined")');
        $this->getSession()->wait(5000, '(0 === jQuery.active)');
    }

    /**
     * @Given /^I am logged in as user$/
     */
    public function iAmLoggedInAsUser()
    {
        $this->getSession()->visit($this->locatePath($this->getMinkParameter('base_url').'/login'));
        $this->fillField('username', $this->testUsername);
        $this->fillField('_password', $this->testPassword);
        $this->pressButton('_submit');
    }

    /**
     * @Given /^I wait for (\d+) seconds$/
     */
    public function iWaitForSeconds($seconds)
    {
        $this->getSession()->wait(1000 * $seconds);
    }

    /**
     * Fills in form field with specified id|name|label|value.
     *
     * @When /^(?:|I )edit inline "(?P<field>(?:[^"]|\\")*)" with "(?P<value>(?:[^"]|\\")*)"$/
     */
    public function iEditInline($field, $value)
    {
        $field = $this->fixStepArgument($field);
        $value = $this->fixStepArgument($value);

        $cssSelector = new CssSelector();
        $xPath = $cssSelector->toXPath($field);
        $this->getSession()->getDriver()->setValue($xPath, $value);
        $xPath = $cssSelector->toXPath('.editable-submit');
        $this->getSession()->getDriver()->click($xPath);
    }

    /**
     * @Given /^I switch to iframe "([^"]*)"$/
     */
    public function iSwithToIframe($arg1 = null)
    {
        $this->getSession()->switchToIFrame($arg1);
    }

    /**
     * Fills in form field with specified id|name|label|value.
     *
     * @When /^(?:|I )add inline tag "(?P<field>(?:[^"]|\\")*)" with "(?P<value>(?:[^"]|\\")*)"$/
     */
    public function iAddInlineTag($field, $value)
    {
        $field = $this->fixStepArgument($field);
        $value = $this->fixStepArgument($value);

        $cssSelector = new CssSelector();
        $xPath = $cssSelector->toXPath($field);
        $this->getSession()->getDriver()->setValue($xPath, $value);
        $xPath = $cssSelector->toXPath('.editable-submit');
        $this->getSession()->getDriver()->click($xPath);
        $this->iWaitForSeconds(0.2);
    }

    /**
     * Click on the element with the provided CSS Selector.
     *
     * @When /^I click on the element with css selector "([^"]*)"$/
     */
    public function iClickOnTheElementWithCSSSelector($cssSelector)
    {
        $session = $this->getSession();
        $element = $session->getPage()->find(
            'xpath',
            $session->getSelectorsHandler()->selectorToXpath('css', $cssSelector) // just changed xpath to css
        );
        if (null === $element) {
            throw new \InvalidArgumentException(sprintf('Could not evaluate CSS Selector: "%s"', $cssSelector));
        }

        $element->click();
    }

    /**
     * Fills in form field with specified id|name|label|value.
     *
     * @When /^(?:|I )select2 "(?P<field>(?:[^"]|\\")*)" with "(?P<value>(?:[^"]|\\")*)"$/
     */
    public function iSelectTwo($field, $value)
    {
        $field = $this->fixStepArgument($field);
        $value = $this->fixStepArgument($value);

        $cssSelector = new CssSelector();
        $xPath = $cssSelector->toXPath($field.' a');

        $this->iWaitForSeconds(0.5);
        $this->getSession()->wait(1000, "jQuery('".$field."').is(':visible')");
        $this->getSession()->getDriver()->click($xPath);

        //$xPath = $cssSelector->toXPath('#s2id_autogen4_search');
        $this->getSession()->getDriver()->setValue($xPath, '');
        $this->getSession()->getDriver()->setValue($xPath, $value);

        $this->iWaitForAjaxToFinish();
        $this->getSession()->wait(1000, "jQuery('.select2-highlighted').is(':visible')");

        if ($this->getSession()->evaluateScript("$('.select2-highlighted:visible').length;") == '1') {
            $xPath = $cssSelector->toXPath('.select2-highlighted');
            $this->getSession()->getDriver()->click($xPath);
        } else {
            //both types of selections works...sometimes
            $this->getSession()->evaluateScript("$('.select2-input:visible').val('".$value."').trigger('keyup-change');");
            $this->getSession()->wait(2000, "jQuery('.select2-highlighted').is(':visible')");
            $xPath = $cssSelector->toXPath('.select2-highlighted');
            $this->getSession()->getDriver()->click($xPath);
        }
    }

    /**
     * Clicks button with specified id.
     *
     * @When /^(?:|I )click button "(?P<button>(?:[^"]|\\")*)"$/
     */
    public function iClickButton($button)
    {
        $cssSelector = new CssSelector();
        $xPath = $cssSelector->toXPath('button#'.$button);
        $this->getSession()->getDriver()->click($xPath);
    }

    /**
     * @Given /^I switch to window$/
     */
    public function iSwitchToWindow($arg1 = null)
    {
        $this->getSession()->switchToWindow(null);
    }

    /**
     * Fills in form field with specified id|name|label|value.
     *
     * @When /^(?:|I )select inline "(?P<field>(?:[^"]|\\")*)" with "(?P<value>(?:[^"]|\\")*)"$/
     */
    public function iSelectInline($field, $value)
    {
        $field = $this->fixStepArgument($field);
        $value = $this->fixStepArgument($value);

        $cssSelector = new CssSelector();
        $xPath = $cssSelector->toXPath($field);

        $this->getSession()->getDriver()->selectOption($xPath, $value);
        $xPath = $cssSelector->toXPath('.editable-submit');
        $this->getSession()->getDriver()->click($xPath);
    }

    /**
     * @param string $radioLabel
     *
     * @throws ElementNotFoundException
     * @Given /^I select the "([^"]*)" radio button$/
     */
    public function iSelectTheRadioButton($radioLabel)
    {
        $radioButton = $this->getSession()->getPage()->findField($radioLabel);
        if (null === $radioButton) {
            throw new ElementNotFoundException($this->getSession(), 'form field', 'id|name|label|value', $radioLabel);
        }
        $this->getSession()->getDriver()->click($radioButton->getXPath());
    }

    /**
     * Fills in form field with specified id|name|label|value.
     *
     * @When /^(?:|I )select2 tag "(?P<field>(?:[^"]|\\")*)" with "(?P<value>(?:[^"]|\\")*)"$/
     */
    public function iSelectTwoTag($field, $value)
    {
        $field = $this->fixStepArgument($field . ' input');
        $value = $this->fixStepArgument($value);

        $cssSelector = new CssSelector();
        $xPath = $cssSelector->toXPath($field);
        $this->getSession()->getDriver()->setValue($xPath, $value);
    }
}
