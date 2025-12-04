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
    private const TIER1_EMPLOYEE_RATE = 5.5;
    private const TIER1_EMPLOYER_RATE = 13.5;
    private const TIER2_RATE = 5.0;
    private const TIER3_MAX_RATE = 16.5;

    public function calculate(float $monthlyIncome): array
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
            'gross_income' => round($monthlyIncome, 2),
            'tax' => round($totalTax, 2),
            'net_income' => round($monthlyIncome - $totalTax, 2),
            'effective_rate' => round(($totalTax / $monthlyIncome) * 100, 2),
            'breakdown' => $breakdown
        ];
    }

    public function calculateTier1(float $basicSalary): array
    {
        $employeeContribution = ($basicSalary * self::TIER1_EMPLOYEE_RATE) / 100;
        $employerContribution = ($basicSalary * self::TIER1_EMPLOYER_RATE) / 100;
        $totalContribution = $employeeContribution + $employerContribution;

        return [
            'basic_salary' => round($basicSalary, 2),
            'employee_contribution' => round($employeeContribution, 2),
            'employee_rate' => self::TIER1_EMPLOYEE_RATE,
            'employer_contribution' => round($employerContribution, 2),
            'employer_rate' => self::TIER1_EMPLOYER_RATE,
            'total_contribution' => round($totalContribution, 2),
            'total_rate' => self::TIER1_EMPLOYEE_RATE + self::TIER1_EMPLOYER_RATE,
            'tax_deductible' => round($employeeContribution, 2)
        ];
    }

    public function calculateTier2(float $basicSalary): array
    {
        $contribution = ($basicSalary * self::TIER2_RATE) / 100;

        return [
            'basic_salary' => round($basicSalary, 2),
            'contribution' => round($contribution, 2),
            'rate' => self::TIER2_RATE,
            'tax_deductible' => round($contribution, 2)
        ];
    }

    public function calculateTier3(float $basicSalary, float $contributionRate = 0): array
    {
        // Ensure contribution rate doesn't exceed maximum
        $actualRate = min($contributionRate, self::TIER3_MAX_RATE);
        $contribution = ($basicSalary * $actualRate) / 100;
        $maxContribution = ($basicSalary * self::TIER3_MAX_RATE) / 100;

        return [
            'basic_salary' => round($basicSalary, 2),
            'contribution' => round($contribution, 2),
            'rate' => $actualRate,
            'max_deductible_rate' => self::TIER3_MAX_RATE,
            'max_deductible_amount' => round($maxContribution, 2),
            'tax_deductible' => round($contribution, 2),
            'is_at_max' => $actualRate >= self::TIER3_MAX_RATE
        ];
    }

    public function calculateAllPensions(float $basicSalary, float $tier3Rate = 0): array
    {
        $tier1 = $this->calculateTier1($basicSalary);
        $tier2 = $this->calculateTier2($basicSalary);
        $tier3 = $this->calculateTier3($basicSalary, $tier3Rate);

        $totalEmployeeContribution = $tier1['employee_contribution'] + $tier2['contribution'] + $tier3['contribution'];
        $totalTaxDeductible = $tier1['tax_deductible'] + $tier2['tax_deductible'] + $tier3['tax_deductible'];

        return [
            'basic_salary' => round($basicSalary, 2),
            'tier1' => $tier1,
            'tier2' => $tier2,
            'tier3' => $tier3,
            'total_employee_contribution' => round($totalEmployeeContribution, 2),
            'total_employer_contribution' => round($tier1['employer_contribution'], 2),
            'total_contributions' => round($totalEmployeeContribution + $tier1['employer_contribution'], 2),
            'total_tax_deductible' => round($totalTaxDeductible, 2)
        ];
    }

    public function calculateWithPensions(float $basicSalary, float $tier3Rate = 0, array $otherAllowances = []): array
    {
        // Calculate all pension contributions
        $pensions = $this->calculateAllPensions($basicSalary, $tier3Rate);
        
        // Calculate total other allowances (if any)
        $totalAllowances = array_sum($otherAllowances);
        
        // Gross income = basic salary + allowances
        $grossIncome = $basicSalary + $totalAllowances;
        
        // Taxable income = gross income - total tax deductible pension contributions
        $taxableIncome = $grossIncome - $pensions['total_tax_deductible'];
        
        // Calculate PAYE on taxable income
        $taxResult = $this->calculate($taxableIncome);
        
        // Net income = gross income - employee pension contributions - tax
        $netIncome = $grossIncome - $pensions['total_employee_contribution'] - $taxResult['tax'];

        return [
            'basic_salary' => round($basicSalary, 2),
            'allowances' => $otherAllowances,
            'total_allowances' => round($totalAllowances, 2),
            'gross_income' => round($grossIncome, 2),
            'pensions' => $pensions,
            'taxable_income' => round($taxableIncome, 2),
            'paye_tax' => round($taxResult['tax'], 2),
            'tax_breakdown' => $taxResult['breakdown'],
            'effective_tax_rate' => round(($taxResult['tax'] / $grossIncome) * 100, 2),
            'total_deductions' => round($pensions['total_employee_contribution'] + $taxResult['tax'], 2),
            'net_income' => round($netIncome, 2),
            'take_home_percentage' => round(($netIncome / $grossIncome) * 100, 2)
        ];
    }

    private function formatRange(float $start, float $end): string
    {
        if ($end >= PHP_FLOAT_MAX) {
            return "Above GHS " . number_format($start, 2);
        }
        return "GHS " . number_format($start, 2) . " - GHS " . number_format($end, 2);
    }

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

// Usage examples:
$calculator = new Taxcalculator();

echo "=== PENSION CALCULATIONS ===\n\n";

// Example 1: Calculate Tier 1 only
$tier1 = $calculator->calculateTier1(5000);
echo "Tier 1 (SSNIT) for GHS 5,000 basic salary:\n";
echo "Employee: GHS " . number_format($tier1['employee_contribution'], 2) . " ({$tier1['employee_rate']}%)\n";
echo "Employer: GHS " . number_format($tier1['employer_contribution'], 2) . " ({$tier1['employer_rate']}%)\n";
echo "Total: GHS " . number_format($tier1['total_contribution'], 2) . "\n\n";

// Example 2: Calculate all pensions
$allPensions = $calculator->calculateAllPensions(5000, 10);
echo "All Pensions for GHS 5,000 basic salary (with 10% Tier 3):\n";
echo "Total Employee Contribution: GHS " . number_format($allPensions['total_employee_contribution'], 2) . "\n";
echo "Total Tax Deductible: GHS " . number_format($allPensions['total_tax_deductible'], 2) . "\n\n";

// Example 3: Complete calculation with pensions
$complete = $calculator->calculateWithPensions(5000, 10, ['transport' => 500, 'housing' => 1000]);
echo "=== COMPLETE SALARY BREAKDOWN ===\n";
echo "Basic Salary: GHS " . number_format($complete['basic_salary'], 2) . "\n";
echo "Allowances: GHS " . number_format($complete['total_allowances'], 2) . "\n";
echo "Gross Income: GHS " . number_format($complete['gross_income'], 2) . "\n";
echo "Pension Deductions: GHS " . number_format($complete['pensions']['total_employee_contribution'], 2) . "\n";
echo "Taxable Income: GHS " . number_format($complete['taxable_income'], 2) . "\n";
echo "PAYE Tax: GHS " . number_format($complete['paye_tax'], 2) . "\n";
echo "Net Income: GHS " . number_format($complete['net_income'], 2) . "\n";
echo "Take Home: " . $complete['take_home_percentage'] . "%\n";