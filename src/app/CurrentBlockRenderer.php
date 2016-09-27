<?php namespace rtens\steps\app;

use rtens\domin\delivery\RendererRegistry;
use rtens\domin\delivery\web\Element;
use rtens\domin\delivery\web\HeadElements;
use rtens\domin\delivery\web\renderers\link\LinkPrinter;
use rtens\domin\delivery\web\Url;
use rtens\steps\model\Steps;
use rtens\steps\model\Time;
use rtens\steps\projecting\CurrentBlock;

class CurrentBlockRenderer extends TransformingRenderer {
    /**
     * @var LinkPrinter
     */
    private $links;

    public function __construct(RendererRegistry $renderers, LinkPrinter $links) {
        parent::__construct($renderers);
        $this->links = $links;
    }

    /**
     * @param mixed $value
     * @return bool
     */
    public function handles($value) {
        return $value instanceof CurrentBlock;
    }

    /**
     * @param CurrentBlock $value
     * @return mixed
     */
    protected function transform($value) {
        $nextBlock = $value->getNextBlock();

        if (!count($value->getBlocks()) && !$nextBlock) {
            $images = [
                'https://cdn.meme.am/instances/28566014.jpg',
                'https://storage.googleapis.com/imgfave/image_cache/1268123271323901.png',
                'http://memeshappen.com/media/created/Finally-It39s-Done-meme-27652.jpg',
                'http://superlol.com/wp-content/uploads/2014/08/I-am-done-for-today.jpg',
                'https://pbs.twimg.com/profile_images/3445962980/67cdf2cc491a460eac7dc9ee120da6be.jpeg',
                'http://m.memegen.com/0oxzd7.jpg',
                'http://s2.quickmeme.com/img/99/99cf2e8d7851a6059fbd1663def73f7fa2a45bc0ab4e7bce7fd237bda44558b7.jpg',
                'http://cdn.quotesgram.com/small/88/45/1747575118-You-Can-Do-It-Meme-Puppy-1.jpg'
            ];

            return [
                new Element('img', [
                    'src' => $images[array_rand($images)],
                    'style' => 'max-width: 100%; max-height: 20em; margin-bottom: 1em '
                ], []),
                new Element('a', [
                    'class' => 'btn btn-success btn-lg',
                    'href' => Url::relative('listGoals')
                ], ['Plan more'])
            ];
        } else if (!count($value->getBlocks())) {
            return [
                new Element('h2', [], [$value->getUnitsLeft() . ' units left']),
                new Element('p', [], ['Next up:']),
                new Element('h1', [], [
                    $nextBlock->getGoalName() .
                    ($nextBlock->getNextStep() ? ': ' . $nextBlock->getNextStep() : '')
                ]),
                new Element('a', [
                    'class' => 'btn btn-success btn-lg',
                    'href' => Url::relative('startNextBlock')
                ], ['Start ' . $nextBlock->getUnits() . ' unit'])
            ];
        }
        $block = $value->getBlocks()[0];

        $elements = [
            new Element('div', ['class' => 'pull-right'], $this->links->createDropDown($block, 'Actions')),
            new Element('h1', [], [
                $block->getGoalName() .
                ($block->getNextStep() ? ': ' . $block->getNextStep() : '')
            ]),
            $block->getUnits() . ' unit' . ($block->getUnits() == 1 ? '' : 's') . ', ' .
            'started @' . $block->getStarted()->format('Y-m-d H:i')
        ];

        $secondsLeft = ($block->getUnits() * Steps::UNIT_SECONDS) - (Time::now()->getTimestamp() - $block->getStarted()->getTimestamp());

        if ($secondsLeft > 0) {
            $elements[] = [
                new Element('div', ['class' => 'countdown', 'style' => 'margin-top: 3em;']),
                new Element('script', [], ['
                var clock = $(".countdown").FlipClock(' . $secondsLeft . ', {
                    countdown: true,
                    clockFace: "MinuteCounter",
                    stop: function () {
                        onStop();
                        onStop = function () {};
                    }
                });
                var audio = new Audio("http://soundbible.com/grab.php?id=1477&type=mp3");
                var onStop = function () {
                    audio.play();
                };
                '])
            ];
        } else {
            $elements[] = [
                new Element('div', ['class' => 'countdown', 'style' => 'display: none; margin-top: 3em;']),
                new Element('script', [], ['
                var snooze = function(minutes) {
                    $(".countdown").show();
                    $(".timeUp").hide();

                    var clock = $(".countdown").FlipClock(minutes * 60, {
                        countdown: true,
                        clockFace: "MinuteCounter",
                        stop: function () {
                            onStop();
                            onStop = function () {};
                        }
                    });
                    var audio = new Audio("http://soundbible.com/grab.php?id=1477&type=mp3");
                    var onStop = function () {
                        audio.play();
                    };
                };
                ']),
                new Element('h3', ["class" => "timeUp"], [
                    "Time's up! (" . round($block->getSpentUnits(), 1) . ' units spent)',
                    new Element('a', ['class' => 'btn btn-warning', 'onclick' => 'snooze(5)'], ['Snooze 5']),
                    new Element('a', ['class' => 'btn btn-danger', 'onclick' => 'snooze(10)'], ['Snooze 10'])
                ])
            ];
        }

        $elements[] = new Element('a', [
            'class' => 'btn btn-success btn-lg',
            'href' => Url::relative('finishBlock', ['block' => ['key' => $block->getBlock()]])
        ], ['Finish!']);

        return $elements;
    }

    public function headElements($value) {
        return array_merge(parent::headElements($value), [
            HeadElements::jquery(),
            HeadElements::script('https://cdn.rawgit.com/objectivehtml/FlipClock/master/compiled/flipclock.min.js'),
            HeadElements::style('https://cdn.rawgit.com/objectivehtml/FlipClock/master/compiled/flipclock.css')
        ]);
    }

}