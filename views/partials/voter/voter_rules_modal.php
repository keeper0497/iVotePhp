<div class="modal" id="rulesModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999;">
    <div class="modal-content" style="background: white; margin: 3% auto; padding: 0; width: 90%; max-width: 900px; border-radius: 12px; box-shadow: 0 20px 60px rgba(0,0,0,0.3); overflow: hidden;">
        <div style="background: linear-gradient(135deg, #4f46e5, #7c3aed); padding: 20px; position: relative;">
            <span class="close" onclick="closeModal('rulesModal')" style="position: absolute; top: 15px; right: 20px; color: white; font-size: 32px; font-weight: bold; cursor: pointer;">&times;</span>
            <h2 style="color: white; margin: 0;">📜 How to Cast Your Vote</h2>
        </div>
        
        <div style="padding: 30px; max-height: 70vh; overflow-y: auto;">
            <div class="steps-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px;">
                <div class="step-box" style="padding: 20px; background: #f0f9ff; border-radius: 8px; text-align: center;">
                    <div class="step-icon" style="font-size: 3rem; color: #0ea5e9; margin-bottom: 10px;">✈️</div>
                    <div class="step-number" style="font-weight: bold; color: #1e40af; margin-bottom: 5px;">Step 3</div>
                    <div class="step-title" style="font-weight: 600; color: #1e293b; margin-bottom: 10px;">Submit Your Vote</div>
                    <div class="step-description" style="color: #64748b; font-size: 0.9rem;">
                        Review your selections carefully and confirm submission to cast your official vote.
                    </div>
                </div>
                
                <div class="step-box" style="padding: 20px; background: #f0f9ff; border-radius: 8px; text-align: center;">
                    <div class="step-icon" style="font-size: 3rem; color: #0ea5e9; margin-bottom: 10px;">🛡️</div>
                    <div class="step-number" style="font-weight: bold; color: #1e40af; margin-bottom: 5px;">Step 4</div>
                    <div class="step-title" style="font-weight: 600; color: #1e293b; margin-bottom: 10px;">Secure Confirmation</div>
                    <div class="step-description" style="color: #64748b; font-size: 0.9rem;">
                        Receive confirmation that your vote has been securely recorded in the system.
                    </div>
                </div>
                
                <div class="step-box" style="padding: 20px; background: #f0f9ff; border-radius: 8px; text-align: center;">
                    <div class="step-icon" style="font-size: 3rem; color: #0ea5e9; margin-bottom: 10px;">📊</div>
                    <div class="step-number" style="font-weight: bold; color: #1e40af; margin-bottom: 5px;">Step 5</div>
                    <div class="step-title" style="font-weight: 600; color: #1e293b; margin-bottom: 10px;">Track Progress</div>
                    <div class="step-description" style="color: #64748b; font-size: 0.9rem;">
                        Monitor voting turnout updates and ensure transparency of the election.
                    </div>
                </div>
                
                <div class="step-box" style="padding: 20px; background: #f0f9ff; border-radius: 8px; text-align: center;">
                    <div class="step-icon" style="font-size: 3rem; color: #0ea5e9; margin-bottom: 10px;">✅</div>
                    <div class="step-number" style="font-weight: bold; color: #1e40af; margin-bottom: 5px;">Step 6</div>
                    <div class="step-title" style="font-weight: 600; color: #1e293b; margin-bottom: 10px;">Finalize Process</div>
                    <div class="step-description" style="color: #64748b; font-size: 0.9rem;">
                        Ensure all procedures are followed and the voting process is officially completed.
                    </div>
                </div>
            </div>
            
            <div class="dont-show" style="margin-top: 30px; text-align: center; padding: 15px; background: #fef3c7; border-radius: 8px;">
                <input type="checkbox" id="dontShowAgain" style="margin-right: 8px;">
                <label for="dontShowAgain" style="color: #92400e; font-weight: 500;">Don't show this again</label>
            </div>
        </div>
    </div>
</div>
