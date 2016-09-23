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
        if (!count($value->getBlocks())) {
            return new Element('a', [
                'class' => 'btn btn-success btn-lg',
                'href' => Url::relative('startNextBlock')
            ], ['Start next Block']);
        }
        $block = $value->getBlocks()[0];

        $elements = [
            new Element('div', ['class' => 'pull-right'], $this->links->createDropDown($block, 'Actions')),
            new Element('h1', [], [
                $block->getGoalName(),
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
                var onStop = function () {
                    var audio = new Audio("http://soundbible.com/grab.php?id=1477&type=mp3");
                    audio.play();
                };
                '])
            ];
        } else {
            $elements[] = new Element('h3', [], ["Time's up! (" . round($block->getSpentUnits(), 1) . ' units spent)']);
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