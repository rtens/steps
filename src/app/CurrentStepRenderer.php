<?php
namespace rtens\steps2\app;

use rtens\domin\delivery\RendererRegistry;
use rtens\domin\delivery\web\Element;
use rtens\domin\delivery\web\HeadElements;
use rtens\domin\delivery\web\WebRenderer;
use rtens\steps2\domain\Step;

class CurrentStepRenderer implements WebRenderer {
    /**
     * @var RendererRegistry
     */
    private $renderers;

    /**
     * @param RendererRegistry $renderers
     */
    public function __construct(RendererRegistry $renderers) {
        $this->renderers = $renderers;
    }

    /**
     * @param mixed $value
     * @return bool
     */
    public function handles($value) {
        return $value instanceof Step && $value->isBeingTaken();
    }

    /**
     * @param Step $step
     * @return mixed
     */
    public function render($step) {
        $elements = [
            new Element('h2', [], [
                $this->renderValue($step->getGoal())
            ])
        ];

        if ($step->getUnitsLeft() > 0) {
            $elements[] = new Element('div', ['class' => 'text-center'], [
                new Element('input', [
                    'type' => 'text',
                    'class' => 'timer'
                ])
            ]);
            $elements[] = new Element('script', [], ['
                    var size = Math.min($(".timer").parent().width(), $(window).height() - 100);
                    var left = ' . $step->getUnitsLeft() * Step::$UNIT_SECS . ' * 1000;
                    var start = Date.now();

                    var update = function() {
                        var value = Math.floor((left - Date.now() + start) / 1000);
                        $(".timer").val(value).trigger("change");

                        if (value <= 0) {
                            clearInterval(interval);
                            audio.play();

                            // Remind every minute
                            setInterval(function() { audio.play(); }, 60000);
                        }
                    };
                    var interval = setInterval(update, 1000);

                    $(".timer").knob({
                        max: ' . $step->getUnits() * Step::$UNIT_SECS . ',
                        readOnly: true,
                        width: size,
                        height: size,
                        format: function (i) {
                            var min = Math.floor(i/60);
                            var sec = i%60;
                            return (min>0 ? min + ":" : "") + (sec<10 ? "0" : "") + (i%60);
                        }
                    });

                    update();

                    var audio = new Audio("http://soundbible.com/grab.php?id=1477&type=mp3");
                ']);
        }

        return (string)new Element('div', [], $elements);
    }

    /**
     * @param mixed $value
     * @return array|Element[]
     */
    public function headElements($value) {
        return [
            HeadElements::jquery(),
            HeadElements::script('http://anthonyterrien.com/demo/knob/jquery.knob.min.js'),
        ];
    }

    private function renderValue($value) {
        return $this->renderers->getRenderer($value)->render($value);
    }
}
