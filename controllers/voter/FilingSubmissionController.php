    <?php
    // Filing Submission Controller

    class FilingSubmissionController {
        private $conn;
        
        public function __construct($connection) {
            $this->conn = $connection;
        }
        
        public function submitMainOrgFiling($userId, $postData, $files) {
            // require_once __DIR__ . '/../../helpers/voter/FileHelper.php'; 
            
            $uploadDir = "uploads/";
            
            // Upload files
            $profile_pic = FileHelper::uploadFile('profile_pic', $uploadDir, $files) ?? '';
            $comelec_form_1 = FileHelper::uploadFile('comelec_form_1', $uploadDir, $files) ?? '';
            $recommendation_letter = FileHelper::uploadFile('CertificateOfRecommendation', $uploadDir, $files) ?? '';
            $prospectus = FileHelper::uploadFile('prospectus', $uploadDir, $files) ?? '';
            $clearance = FileHelper::uploadFile('clearance', $uploadDir, $files) ?? '';
            $coe = FileHelper::uploadFile('coe', $uploadDir, $files) ?? '';
            $certificate_of_candidacy = FileHelper::uploadFile('CertificateofCandidacy', $uploadDir, $files) ?? '';
            
            // --- FIX START: Assign all values to variables ---
            $organization = $postData['organization'] ?? '';
            $first_name = $postData['first_name'] ?? '';
            $middle_name = $postData['middle_name'] ?? '';
            $last_name = $postData['last_name'] ?? '';
            $nickname = $postData['nickname'] ?? '';
            $age = intval($postData['age'] ?? 0); // Must be a variable
            $gender = $postData['gender'] ?? '';
            $dob = $postData['dob'] ?? '';
            $college = $postData['college'] ?? '';
            $year = intval($postData['year'] ?? 0); // Must be a variable
            $program = $postData['program'] ?? '';
            $phone = $postData['phone'] ?? '';
            $email = $postData['email'] ?? '';
            $position = $postData['position'] ?? '';
            $partylist = $postData['partylist'] ?? '';
            $permanent_address = $postData['permanent_address'] ?? '';
            $temporary_address = $postData['temporary_address'] ?? '';
            $residency_years = intval($postData['residency_years'] ?? 0); // Must be a variable
            $residency_semesters = intval($postData['residency_semesters'] ?? 0); // Must be a variable
            $semester_year = $postData['semester_year'] ?? '';

            $stmt = $this->conn->prepare("INSERT INTO main_org_candidates
                (user_id, organization, first_name, middle_name, last_name, nickname, age, gender, dob, college, year, program, phone, email, position, partylist, permanent_address, temporary_address, residency_years, residency_semesters, semester_year, profile_pic, comelec_form_1, recommendation_letter, prospectus, clearance, coe, certificate_of_candidacy, status, filing_date) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Pending', NOW())");
            
            if (!$stmt) {
                return ['success' => false, 'message' => "Database error: " . $this->conn->error];
            }
            
            $stmt->bind_param(
                "isssssisssisssssssiissssssss", // CORRECTED BIND STRING (1 user_id, 5 names, 1 age(i), 3 fields, 1 year(i), 7 fields, 2 residency(i,i), 7 files/fields)
                $userId,
                $organization, // <--- Now a variable
                $first_name,   // <--- Now a variable
                $middle_name,  // <--- Now a variable
                $last_name,    // <--- Now a variable
                $nickname,     // <--- Now a variable
                $age,          // <--- Now a variable (intval result)
                $gender,       // <--- Now a variable
                $dob,          // <--- Now a variable
                $college,      // <--- Now a variable
                $year,         // <--- Now a variable (intval result)
                $program,      // <--- Now a variable
                $phone,        // <--- Now a variable
                $email,        // <--- Now a variable
                $position,     // <--- Now a variable
                $partylist,    // <--- Now a variable
                $permanent_address, // <--- Now a variable
                $temporary_address, // <--- Now a variable
                $residency_years,   // <--- Now a variable (intval result)
                $residency_semesters, // <--- Now a variable (intval result)
                $semester_year,     // <--- Now a variable
                $profile_pic,
                $comelec_form_1,
                $recommendation_letter,
                $prospectus,
                $clearance,
                $coe,
                $certificate_of_candidacy
            );
            
            if ($stmt->execute()) {
                $stmt->close();
                return ['success' => true, 'message' => "Main Organization filing submitted successfully!"];
            } else {
                $error = $stmt->error;
                $stmt->close();
                error_log("Main Org Filing Error: " . $error);
                return ['success' => false, 'message' => "Error submitting filing: " . $error];
            }
        }
        
        public function submitSubOrgFiling($userId, $postData) {
            $organization = $postData['organization'] ?? '';
            $first_name_sub = $postData['first_name_sub'] ?? '';
            $middle_name_sub = $postData['middle_name_sub'] ?? '';
            $last_name_sub = $postData['last_name_sub'] ?? '';
            $position_sub = $postData['position_sub'] ?? 'Representative';
            $year_sub = intval($postData['year_sub'] ?? 0);
            $block_address_sub = $postData['block_address_sub'] ?? '';
            
            if (empty($organization) || empty($first_name_sub) || empty($last_name_sub) || empty($position_sub) || $year_sub < 1) {
                return ['success' => false, 'message' => "Please fill in all required fields correctly."];
            }
            
            $stmt = $this->conn->prepare("INSERT INTO sub_org_candidates
                (user_id, organization, last_name, first_name, middle_name, year, block_address, position_sub, status, filing_date) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'Pending', NOW())");
            
            if (!$stmt) {
                return ['success' => false, 'message' => "Database error: " . $this->conn->error];
            }
            
            $stmt->bind_param(
                "issssiss",
                $userId,
                $organization,
                $last_name_sub,
                $first_name_sub,
                $middle_name_sub,
                $year_sub,
                $block_address_sub,
                $position_sub
            );
            
            if ($stmt->execute()) {
                $stmt->close();
                return ['success' => true, 'message' => "Sub Organization filing submitted successfully!"];
            } else {
                $error = $stmt->error;
                $stmt->close();
                error_log("Sub Org Filing Error: " . $error);
                return ['success' => false, 'message' => "Error submitting filing: " . $error];
            }
        }
    }
    ?>