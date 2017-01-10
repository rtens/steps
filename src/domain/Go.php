<?php
namespace rtens\steps2\domain;

class Go extends PlanList {

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
            if ($plan->getNextSteps()) {
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
        return new \ReflectionClass(Plan::class);
    }
}