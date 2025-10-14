<!-- Add User Modal -->
<div id="addModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal('addModal')">&times;</span>
        <h3>Add User</h3>
        <form method="POST">
            <input type="email" name="email" placeholder="Email" required/>
            <input type="text" name="student_id" placeholder="Student ID" required/>
            <input type="text" name="password" placeholder="Password" required/>
            <select name="role" required>
                <option value="">Select Role</option>
                <option value="voter">Voter</option>
                <option value="admin">Admin</option>
                <option value="candidate">Candidate</option>
            </select>
            <select name="college" required>
                <option value="">Select College</option>
                <option value="CICT">CICT (College of Information and Communications Technology)</option>
                <option value="CBA">CBA (College of Business and Accountancy)</option>
                <option value="CHS">CHS (College of Health and Sciences)</option>
                <option value="CEA">CEA (College of Engineering and Architecture)</option>
                <option value="COS">COS (College of Science)</option>
                <option value="CHUMSS">CHUMSS (College of Humanities and Social Sciences)</option>
                <option value="CAF">CAF (College of Agriculture and Fisheries)</option>
                <option value="CIT">CIT (College of Information Technology)</option>
                <option value="COED">COED (College of Education)</option>
            </select>
            <button class="btn" type="submit" name="addUser">Save</button>
        </form>
    </div>
</div>

<!-- Edit User Modal -->
<div id="editModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal('editModal')">&times;</span>
        <h3>Edit User</h3>
        <form method="POST">
            <input type="hidden" name="id" id="edit_id"/>
            <input type="email" name="email" id="edit_email" required/>
            <input type="text" name="student_id" id="edit_student_id" required/>
            <input type="text" name="password" id="edit_password" placeholder="Leave blank to keep current password"/>
            <select name="role" id="edit_role" required>
                <option value="voter">Voter</option>
                <option value="admin">Admin</option>
                <option value="candidate">Candidate</option>
            </select>
            <select name="college" id="edit_college" required>
                <option value="CICT">CICT (College of Information and Communications Technology)</option>
                <option value="CBA">CBA (College of Business and Accountancy)</option>
                <option value="CHS">CHS (College of Health and Sciences)</option>
                <option value="CEA">CEA (College of Engineering and Architecture)</option>
                <option value="COS">COS (College of Science)</option>
                <option value="CHUMSS">CHUMSS (College of Humanities and Social Sciences)</option>
                <option value="CAF">CAF (College of Agriculture and Fisheries)</option>
                <option value="CIT">CIT (College of Information Technology)</option>
                <option value="COED">COED (College of Education)</option>
            </select>
            <button class="btn" type="submit" name="editUser">Update</button>
        </form>
    </div>
</div>

<!-- Filing Preview Modal -->
<div id="previewModal" class="modal">
    <div class="modal-content" style="max-width: 800px; width: 90%; background: #fff; border-radius: 16px; padding: 0; box-shadow: 0 20px 60px rgba(0,0,0,0.3); overflow: hidden;">
        <div style="background: linear-gradient(135deg, #4f46e5, #7c3aed); padding: 20px; position: relative;">
            <span class="close" onclick="closeModal('previewModal')" style="position: absolute; top: 15px; right: 20px; color: white; font-size: 32px; font-weight: bold; cursor: pointer; transition: transform 0.2s;">&times;</span>
            <h3 style="color: white; margin: 0; font-size: 1.5rem; font-weight: 600;">Candidate Filing Preview</h3>
        </div>
        
        <div style="padding: 30px; max-height: 70vh; overflow-y: auto;">
            <div id="previewContent"></div>
            
            <form method="post" id="previewForm" style="margin-top: 30px;">
                <input type="hidden" name="filing_id" id="preview_filing_id">
                <input type="hidden" name="type" id="preview_type">
                <input type="hidden" name="action" id="preview_action">
                
                <div style="background: #f8fafc; border-radius: 12px; padding: 20px; border: 2px solid #e2e8f0;">
                    <label for="comment" style="display: block; font-weight: 600; color: #1e293b; margin-bottom: 10px; font-size: 1rem;">
                        <ion-icon name="chatbox-outline" style="vertical-align: middle; font-size: 1.2rem;"></ion-icon>
                        Admin Comment:
                    </label>
                    <textarea 
                        name="comment" 
                        id="comment" 
                        rows="5" 
                        placeholder="Enter your comment, feedback, or reason for decision here..."
                        style="width: 100%; padding: 15px; border: 2px solid #cbd5e1; border-radius: 10px; font-size: 0.95rem; font-family: inherit; resize: vertical; transition: all 0.3s ease; background: white;"
                    ></textarea>
                    <p style="margin: 10px 0 0 0; color: #64748b; font-size: 0.85rem;">
                        <ion-icon name="information-circle-outline" style="vertical-align: middle;"></ion-icon>
                        This comment will be visible to the candidate.
                    </p>
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 15px; margin-top: 25px; padding-top: 20px; border-top: 2px solid #e2e8f0;">
                    <button 
                        type="submit" 
                        class="accept" 
                        onclick="setAction('accept')"
                        style="background: linear-gradient(135deg, #10b981, #059669); color: white; padding: 12px 30px; border: none; border-radius: 10px; font-weight: 600; font-size: 1rem; cursor: pointer; display: flex; align-items: center; gap: 8px; transition: all 0.3s ease; box-shadow: 0 4px 12px rgba(16,185,129,0.3);"
                    >
                        <ion-icon name="checkmark-circle" style="font-size: 1.3rem;"></ion-icon>
                        Accept Application
                    </button>
                    <button 
                        type="submit" 
                        class="reject" 
                        onclick="setAction('reject')"
                        style="background: linear-gradient(135deg, #ef4444, #dc2626); color: white; padding: 12px 30px; border: none; border-radius: 10px; font-weight: 600; font-size: 1rem; cursor: pointer; display: flex; align-items: center; gap: 8px; transition: all 0.3s ease; box-shadow: 0 4px 12px rgba(239,68,68,0.3);"
                    >
                        <ion-icon name="close-circle" style="font-size: 1.3rem;"></ion-icon>
                        Reject Application
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Image Preview Modal -->
<div id="imagePreviewModal" class="modal" onclick="closeImagePreview(event)">
    <div style="position: relative; max-width: 90%; max-height: 90vh; margin: 2% auto;">
        <span onclick="closeModal('imagePreviewModal')" style="position: absolute; top: -40px; right: 0; color: white; font-size: 40px; font-weight: bold; cursor: pointer; z-index: 1001; text-shadow: 0 2px 4px rgba(0,0,0,0.5);">&times;</span>
        <img id="previewImage" src="" style="max-width: 100%; max-height: 85vh; border-radius: 12px; box-shadow: 0 20px 60px rgba(0,0,0,0.5); display: block;">
        <p id="imageFileName" style="color: white; text-align: center; margin-top: 15px; font-size: 1.1rem; text-shadow: 0 2px 4px rgba(0,0,0,0.5);"></p>
    </div>
</div>

<!-- Export Modal -->
<div id="exportModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal('exportModal')">&times;</span>
        <h3>Export Report</h3>
        <form method="POST">
            <div class="export-options">
                <p>Choose export format:</p>
                <div class="radio-group">
                    <label><input type="radio" name="export_type" value="csv" checked> CSV (.csv)</label>
                    <label><input type="radio" name="export_type" value="excel"> Excel (.xlsx)</label>
                </div>
            </div>
            <div class="form-actions">
                <button type="submit" name="exportReport" class="btn">Export</button>
                <button type="button" class="btn" onclick="closeModal('exportModal')">Cancel</button>
            </div>
        </form>
    </div>
</div>