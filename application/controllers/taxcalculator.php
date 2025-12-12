<?php

class Taxcalculator
{
    private array $taxBands = [
        ['limit' => 490, 'rate' => 0],
        ['limit' => 600, 'rate' => 5],      // 490 + 110
        ['limit' => 730, 'rate' => 10],     // 600 + 130
        ['limit' => 3896.67, 'rate' => 17.5], // 730 + 3166.67
        ['limit' => 19896.67, 'rate' => 25],  // 3896.67 + 16000
        ['limit' => 50416.67, 'rate' => 30],  // 19896.67 + 30520
        ['limit' => PHP_FLOAT_MAX, 'rate' => 35] // Above 50416.67
    ];

    // Pension rates
    private $TIER1_EMPLOYEE_RATE = 0;
    private $TIER1_EMPLOYER_RATE = 13.5;
    private $TIER2_RATE = 0;
    private $TIER3_MAX_RATE = 0;

    /**
     * Calculate the tax for a given monthly income
     * 
     * @param float $monthlyIncome The monthly income to calculate the tax for
     * 
     * @return array The tax breakdown
     */
    public function calculate(float $monthlyIncome, bool $calculate = false): array
    {
        if ($monthlyIncome <= 0) {
            return [
                'gross_income' => 0,
                'tax' => 0,
                'net_income' => 0,
                'breakdown' => []
            ];
        }

        $totalTax = 0;
        $remainingIncome = $monthlyIncome;
        $breakdown = [];
        $previousLimit = 0;

        foreach ($this->taxBands as $band) {
            if ($remainingIncome <= 0) {
                break;
            }

            $bandLimit = $band['limit'] - $previousLimit;
            $taxableInThisBand = min($remainingIncome, $bandLimit);
            $taxForThisBand = ($taxableInThisBand * $band['rate']) / 100;

            if ($taxableInThisBand > 0) {
                $breakdown[] = [
                    'range' => $this->formatRange($previousLimit, $band['limit']),
                    'taxable_amount' => round($taxableInThisBand, 2),
                    'rate' => $band['rate'],
                    'tax' => round($taxForThisBand, 2)
                ];

                $totalTax += $taxForThisBand;
                $remainingIncome -= $taxableInThisBand;
            }

            $previousLimit = $band['limit'];
        }

        return [
            'gross_income' => $calculate ? round($monthlyIncome, 2) : 0,
            'tax' => $calculate ? round($totalTax, 2) : 0,
            'net_income' => $calculate ? round($monthlyIncome - $totalTax, 2) : 0,
            'effective_rate' => $calculate ? round(($totalTax / $monthlyIncome) * 100, 2) : 0,
            'breakdown' => $breakdown
        ];
    }

    /**
     * Calculate the Tier 1 pension for a given basic salary
     * 
     * @param float $basicSalary The basic salary to calculate the Tier 1 pension for
     * 
     * @return array The Tier 1 pension breakdown
     */
    public function calculateTier1($basicSalary = 0): array
    {
        $employeeContribution = ($basicSalary * $this->TIER1_EMPLOYEE_RATE) / 100;
        $employerContribution = ($basicSalary * $this->TIER1_EMPLOYER_RATE) / 100;
        $totalContribution = $employeeContribution + $employerContribution;

        return [
            'basic_salary' => !empty($basicSalary) ? round($basicSalary, 2) : 0,
            'employee_contribution' => !empty($employeeContribution) ? round($employeeContribution, 2) : 0,
            'employee_rate' => $this->TIER1_EMPLOYEE_RATE,
            'employer_contribution' => !empty($employerContribution) ? round($employerContribution, 2) : 0,
            'employer_rate' => $this->TIER1_EMPLOYER_RATE,
            'total_contribution' => !empty($totalContribution) ? round($totalContribution, 2) : 0,
            'total_rate' => $this->TIER1_EMPLOYEE_RATE + $this->TIER1_EMPLOYER_RATE,
            'tax_deductible' => !empty($employeeContribution) ? round($employeeContribution, 2) : 0
        ];
    }

    /**
     * Calculate the Tier 2 pension for a given basic salary
     * 
     * @param float $basicSalary The basic salary to calculate the Tier 2 pension for
     * 
     * @return array The Tier 2 pension breakdown
     */
    public function calculateTier2($basicSalary = 0): array
    {
        $contribution = ($basicSalary * $this->TIER2_RATE) / 100;

        return [
            'basic_salary' => !empty($basicSalary) ? round($basicSalary, 2) : 0,
            'contribution' => !empty($contribution) ? round($contribution, 2) : 0,
            'rate' => $this->TIER2_RATE,
            'tax_deductible' => !empty($contribution) ? round($contribution, 2) : 0
        ];
    }

    /**
     * Calculate the Tier 3 pension for a given basic salary
     * 
     * @param float $basicSalary The basic salary to calculate the Tier 3 pension for
     * @param float $contributionRate The contribution rate to calculate the Tier 3 pension for
     * 
     * @return array The Tier 3 pension breakdown
     */
    public function calculateTier3($basicSalary = 0, float $contributionRate = 0): array
    {
        // Ensure contribution rate doesn't exceed maximum
        $actualRate = min($contributionRate, $this->TIER3_MAX_RATE);
        $contribution = ($basicSalary * $actualRate) / 100;
        $maxContribution = ($basicSalary * $this->TIER3_MAX_RATE) / 100;

        return [
            'basic_salary' => !empty($basicSalary) ? round($basicSalary, 2) : 0,
            'contribution' => !empty($contribution) ? round($contribution, 2) : 0,
            'rate' => $actualRate,
            'max_deductible_rate' => $this->TIER3_MAX_RATE,
            'max_deductible_amount' => !empty($maxContribution) ? round($maxContribution, 2) : 0,
            'tax_deductible' => !empty($contribution) ? round($contribution, 2) : 0,
            'is_at_max' => $actualRate >= $this->TIER3_MAX_RATE
        ];
    }

    /**
     * Calculate all the pensions for a given basic salary
     * 
     * @param float $basicSalary The basic salary to calculate the pensions for
     * @param float $tier3Rate The contribution rate to calculate the Tier 3 pension for
     * 
     * @return array The pensions breakdown
     */
    public function calculateAllPensions($basicSalary = 0, float $tier3Rate = 0): array
    {
        $tier1 = $this->calculateTier1($basicSalary);
        $tier2 = $this->calculateTier2($basicSalary);
        $tier3 = $this->calculateTier3($basicSalary, $tier3Rate);

        $totalEmployeeContribution = $tier1['employee_contribution'] + $tier2['contribution'] + $tier3['contribution'];
        $totalTaxDeductible = $tier1['tax_deductible'] + $tier2['tax_deductible'] + $tier3['tax_deductible'];

        return [
            'basic_salary' => !empty($basicSalary) ? round($basicSalary, 2) : 0,
            'tier1' => $tier1,
            'tier2' => $tier2,
            'tier3' => $tier3,
            'total_employee_contribution' => round($totalEmployeeContribution, 2),
            'total_employer_contribution' => round($tier1['employer_contribution'], 2),
            'total_contributions' => round($totalEmployeeContribution + $tier1['employer_contribution'], 2),
            'total_tax_deductible' => round($totalTaxDeductible, 2)
        ];
    }

    /**
     * Calculate the PAYE tax for a given basic salary
     * 
     * @param float $basicSalary The basic salary to calculate the PAYE tax for
     * @param float $tier3Rate The contribution rate to calculate the Tier 3 pension for
     * @param array $otherAllowances The other allowances to calculate the PAYE tax for
     * 
     * @return array The PAYE tax breakdown
     */
    public function calculateWithPensions($basicSalary = 0, float $tier3Rate = 0, array $otherAllowances = [], array $taxRatings = []): array
    {

        $this->TIER1_EMPLOYEE_RATE = (int)($taxRatings['tier1'] ?? 0);
        $this->TIER2_RATE = (int)($taxRatings['tier2'] ?? 0);

        // Calculate all pension contributions
        $pensions = $this->calculateAllPensions($basicSalary, $tier3Rate);
        
        // Calculate total other allowances (if any)
        $totalAllowances = array_sum($otherAllowances);
        
        // Gross income = basic salary + allowances
        $grossIncome = $basicSalary + $totalAllowances;
        
        // Taxable income = gross income - total tax deductible pension contributions
        $taxableIncome = $grossIncome - $pensions['total_tax_deductible'];
        
        // Calculate PAYE on taxable income
        $taxResult = $this->calculate($taxableIncome, $taxRatings['paye'] ?? false);
        
        // Net income = gross income - employee pension contributions - tax
        $netIncome = $grossIncome - $pensions['total_employee_contribution'] - $taxResult['tax'];

        return [
            'basic_salary' => !empty($basicSalary) ? round($basicSalary, 2) : 0,
            'allowances' => $otherAllowances,
            'total_allowances' => !empty($totalAllowances) ? round($totalAllowances, 2) : 0,
            'gross_income' => !empty($grossIncome) ? round($grossIncome, 2) : 0,
            'pensions' => $pensions,
            'taxable_income' => !empty($taxableIncome) ? round($taxableIncome, 2) : 0,
            'paye_tax' => !empty($taxResult['tax']) ? round($taxResult['tax'], 2) : 0,
            'tax_breakdown' => $taxResult['breakdown'],
            'effective_tax_rate' => !empty($grossIncome) ? round(($taxResult['tax'] / $grossIncome) * 100, 2) : 0,
            'total_deductions' => round($pensions['total_employee_contribution'] + $taxResult['tax'], 2),
            'net_income' => !empty($netIncome) ? round($netIncome, 2) : 0,
            'take_home_percentage' => !empty($grossIncome) ? round(($netIncome / $grossIncome) * 100, 2) : 0
        ];
    }

    /**
     * Format the range for the tax breakdown
     * 
     * @param float $start The start of the range
     * @param float $end The end of the range
     * 
     * @return string The formatted range
     */
    private function formatRange(float $start, float $end): string
    {
        if ($end >= PHP_FLOAT_MAX) {
            return "Above GHS " . number_format($start, 2);
        }
        return "GHS " . number_format($start, 2) . " - GHS " . number_format($end, 2);
    }

    /**
     * Calculate the annual tax for a given annual income
     * 
     * @param float $annualIncome The annual income to calculate the annual tax for
     * 
     * @return array The annual tax breakdown
     */
    public function calculateAnnual(float $annualIncome): array
    {
        $monthlyIncome = $annualIncome / 12;
        $monthlyResult = $this->calculate($monthlyIncome);

        return [
            'annual_gross_income' => round($annualIncome, 2),
            'annual_tax' => round($monthlyResult['tax'] * 12, 2),
            'annual_net_income' => round($monthlyResult['net_income'] * 12, 2),
            'effective_rate' => $monthlyResult['effective_rate'],
            'monthly_breakdown' => $monthlyResult
        ];
    }
}