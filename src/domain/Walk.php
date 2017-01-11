<?php
namespace rtens\steps2\domain;

class Walk extends PathList {

    public function hasActivePlan() {
        foreach ($this->getPlans() as $plan) {
            if ($plan->isActive()) {
                return true;
            }
        }
        return false;
    }

    public function hasNextStep() {
        foreach ($this->getPlans() as $plan) {
            if ($plan->getRemainingSteps()) {
                return true;
            }
        }
        return false;
    }

    public function getCurrentStep() {
        foreach ($this->getPlans() as $plan) {
            if ($plan->getCurrentStep()) {
                return $plan->getCurrentStep();
            }
        }
        return null;
    }

    public function isTakingStep() {
        return !!$this->getCurrentStep();
    }

    /**
     * @return \ReflectionClass
     */
    protected function inferDomainObjectClass() {
        return new \ReflectionClass(Path::class);
    }
}