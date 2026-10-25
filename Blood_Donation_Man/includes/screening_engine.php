<?php
/**
 * JeevanRakt - Blood Donor Management System
 * Health Screening Logic Engine
 * Developer: Aryan Verma (Student ID: 241306064)
 *
 * Evaluation Workflow:
 * User Answers -> Rule Checks -> Preliminary Outcome -> Structured Reasons
 */

function evaluate_health_screening($data) {
    $deferral_reasons = [];
    $review_reasons = [];

    // --- 1. Current Health & Symptoms Check (Temporarily Deferred) ---
    if (!empty($data['unwell']) && $data['unwell'] === 'yes') {
        $deferral_reasons[] = "Donor reported currently feeling unwell.";
    }
    if (!empty($data['fever']) && $data['fever'] === 'yes') {
        $deferral_reasons[] = "Donor currently has fever.";
    }
    if (!empty($data['cold_flu']) && $data['cold_flu'] === 'yes') {
        $deferral_reasons[] = "Donor currently has a cold or flu-like illness.";
    }
    if (!empty($data['infection']) && $data['infection'] === 'yes') {
        $deferral_reasons[] = "Donor currently has an active infection.";
    }
    if (!empty($data['antibiotics']) && $data['antibiotics'] === 'yes') {
        $deferral_reasons[] = "Donor is currently taking antibiotics for an active infection.";
    }
    if (!empty($data['alcohol']) && $data['alcohol'] === 'yes') {
        $deferral_reasons[] = "Donor reported recent alcohol consumption or being under the influence.";
    }

    // --- 2. Medical History & Chronic Conditions Check (Medical Review Required) ---
    if (!empty($data['hiv_history']) && $data['hiv_history'] === 'yes') {
        $review_reasons[] = "History or diagnosis of HIV/AIDS reported.";
    }
    if (!empty($data['hepatitis_history']) && $data['hepatitis_history'] === 'yes') {
        $review_reasons[] = "History of Hepatitis B or Hepatitis C reported.";
    }
    if (!empty($data['bleeding_disorder']) && $data['bleeding_disorder'] === 'yes') {
        $review_reasons[] = "Bleeding or clotting disorder history reported.";
    }
    if (!empty($data['heart_condition']) && $data['heart_condition'] === 'yes') {
        $review_reasons[] = "Serious heart condition reported.";
    }
    if (!empty($data['diabetes']) && $data['diabetes'] === 'yes') {
        $review_reasons[] = "Uncontrolled diabetes or insulin therapy reported.";
    }
    if (!empty($data['medical_condition']) && $data['medical_condition'] === 'yes') {
        $review_reasons[] = "Other ongoing serious medical condition reported.";
    }

    // --- 3. Medications Check (Medical Review Required) ---
    if (!empty($data['medication']) && $data['medication'] === 'yes') {
        $review_reasons[] = "Currently taking prescription medication.";
    }
    if (!empty($data['blood_thinner']) && $data['blood_thinner'] === 'yes') {
        $review_reasons[] = "Currently taking blood-thinning medication.";
    }
    if (!empty($data['hiv_meds']) && $data['hiv_meds'] === 'yes') {
        $review_reasons[] = "Taking medication for HIV prevention or treatment.";
    }

    // --- 4. Recent Medical & Dental Procedures (Medical Review Required / Deferred) ---
    if (!empty($data['recent_surgery']) && $data['recent_surgery'] === 'yes') {
        $review_reasons[] = "Undergone surgery recently.";
    }
    if (!empty($data['dental_procedure']) && $data['dental_procedure'] === 'yes') {
        $review_reasons[] = "Undergone a dental procedure recently.";
    }
    if (!empty($data['tattoo']) && $data['tattoo'] === 'yes') {
        $review_reasons[] = "Received a tattoo recently.";
    }
    if (!empty($data['piercing']) && $data['piercing'] === 'yes') {
        $review_reasons[] = "Received a body piercing recently.";
    }
    if (!empty($data['blood_transfusion']) && $data['blood_transfusion'] === 'yes') {
        $review_reasons[] = "Received a blood transfusion recently.";
    }

    // --- 5. Travel & Exposure (Medical Review Required) ---
    if (!empty($data['travel_history']) && $data['travel_history'] === 'yes') {
        $review_reasons[] = "Recent travel outside usual area or to endemic region.";
    }
    if (!empty($data['infection_exposure']) && $data['infection_exposure'] === 'yes') {
        $review_reasons[] = "Recent exposure to someone with an infectious disease.";
    }

    // Note: Smoking / Tobacco is recorded for informational purposes and does not trigger deferral by itself.

    // --- Determine Final Result & Reason String ---
    if (!empty($deferral_reasons)) {
        return [
            'result' => 'Temporarily Deferred',
            'badge_class' => 'bg-danger text-white',
            'icon' => '🔴',
            'summary' => 'Donation should not proceed at this time based on current health status or intoxication.',
            'reasons' => array_merge($deferral_reasons, $review_reasons)
        ];
    } elseif (!empty($review_reasons)) {
        return [
            'result' => 'Medical Review Required',
            'badge_class' => 'bg-warning text-dark',
            'icon' => '🟡',
            'summary' => 'Your answers require additional medical evaluation by qualified blood bank screening staff before proceeding.',
            'reasons' => $review_reasons
        ];
    } else {
        return [
            'result' => 'Proceed to Screening',
            'badge_class' => 'bg-success text-white',
            'icon' => '🟢',
            'summary' => 'No preliminary disqualifying factors identified by questionnaire. Final eligibility will be verified by blood bank staff.',
            'reasons' => ["All preliminary screening criteria met satisfactorily."]
        ];
    }
}
