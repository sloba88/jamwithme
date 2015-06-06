<?php

namespace Jam\CoreBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Jam\CoreBundle\Entity\Genre;
use Jam\CoreBundle\Entity\Instrument;

class LoadInstrumentsData implements FixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $instruments = array(
          "Accordion"
        , "Acoustic Guitar"
        , "Areophones"
        , "Autoharp"
        , "Background Vocals"
        , "Bagpipe"
        , "Balalaika"
        , "Banjo"
        , "Baritone Guitar"
        , "Bass"
        , "Bassoon"
        , "Bongo Drums"
        , "Celesta"
        , "Cello"
        , "Chordophone"
        , "Clarinet"
        , "Classical Guitar"
        , "Clavinet"
        , "Computer"
        , "Conch"
        , "Cornett"
        , "Dan Bau"
        , "Dobro"
        , "Double Bass"
        , "Drum Machine"
        , "Drums"
        , "Eight-string Guitar"
        , "Electric Guitar"
        , "Electronic Organ"
        , "Flamenco Guitar"
        , "Flute"
        , "Guitar"
        , "Guitar Synthesizer"
        , "Hammond"
        , "Hammond Organ"
        , "Harmonica"
        , "Harp"
        , "Harp Guitar"
        , "Kantele"
        , "Kayboard"
        , "Keys"
        , "Lap Steel"
        , "Lute"
        , "Mandolin"
        , "Marimba"
        , "Mellotron"
        , "Membranophones"
        , "Nose Flute"
        , "Oboe"
        , "Ocarina"
        , "Organ"
        , "Pedal Steel"
        , "Percussions"
        , "Piano"
        , "Piccolo Violino"
        , "Recorder"
        , "Reed Organ"
        , "Sampler"
        , "Seven-string Guitar"
        , "Shawm"
        , "Siren"
        , "Sitar"
        , "Slide Guitar"
        , "Steel Guitar"
        , "Suona"
        , "Synth"
        , "Synthesizer"
        , "Tenor Viola"
        , "Tin Whistle"
        , "Trombone"
        , "Trumpet"
        , "Tumpong"
        , "Twelve-string Guitar"
        , "Ukulele"
        , "Whip"
        , "Whistle"
        , "Violin"
        , "Vocals"
        , "Xylophone"
        );

        foreach ($instruments as $i) {
            $instrument = new Instrument();
            $instrument->setName($i);
            $manager->persist($instrument);
        }

        $manager->flush();
    }
}