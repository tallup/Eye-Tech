<x-filament-panels::page>
    <style>
        .profile-container {
            padding: 2rem;
            background: #f8fafc;
            min-height: 100vh;
        }
        .profile-header {
            background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
            border-radius: 1rem;
            padding: 2rem;
            color: white;
            margin-bottom: 2rem;
            box-shadow: 0 10px 25px rgba(220, 38, 38, 0.3);
        }
        .profile-card {
            background: white;
            border-radius: 1rem;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border: 1px solid #e5e7eb;
        }
        .profile-avatar {
            width: 4rem;
            height: 4rem;
            border-radius: 50%;
            background: #f3f4f6;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }
        .profile-info-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.75rem 0;
            border-bottom: 1px solid #f3f4f6;
        }
        .profile-info-row:last-child {
            border-bottom: none;
        }
        .profile-label {
            font-weight: 500;
            color: #6b7280;
            font-size: 0.875rem;
        }
        .profile-value {
            color: #111827;
            font-size: 0.875rem;
        }
        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 500;
        }
        .status-verified {
            background: #dcfce7;
            color: #166534;
        }
        .status-warning {
            background: #fef3c7;
            color: #92400e;
        }
        .status-neutral {
            background: #f3f4f6;
            color: #374151;
        }
        .section-title {
            font-size: 1.125rem;
            font-weight: 600;
            color: #111827;
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid #e5e7eb;
        }
        .grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
        }
        @media (max-width: 768px) {
            .grid-2 {
                grid-template-columns: 1fr;
            }
        }
    </style>
    
    <div class="profile-container">
        <!-- Header -->
        <div class="profile-header">
            <h1 style="font-size: 2.5rem; font-weight: 700; margin-bottom: 0.5rem;">My Profile</h1>
            <p style="color: #fecaca; font-size: 1.125rem;">View your personal information and account details</p>
        </div>

        <!-- Profile Header Card -->
        <div class="profile-card">
            <div style="display: flex; align-items: center; gap: 1rem;">
                <div class="profile-avatar">
                    @if(auth()->user()->profile_picture)
                        <img src="{{ Storage::url(auth()->user()->profile_picture) }}" 
                             alt="{{ auth()->user()->name }}" 
                             style="width: 100%; height: 100%; object-fit: cover;">
                    @else
                        <svg style="width: 2rem; height: 2rem; color: #9ca3af;" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                        </svg>
                    @endif
                </div>
                <div>
                    <h2 style="font-size: 1.5rem; font-weight: 700; color: #111827; margin-bottom: 0.25rem;">
                        {{ auth()->user()->name ?? 'User Profile' }}
                    </h2>
                    <p style="color: #6b7280; margin-bottom: 0.25rem;">
                        {{ auth()->user()->email }}
                    </p>
                    @if(auth()->user()->phone)
                        <p style="color: #6b7280;">
                            {{ auth()->user()->phone }}
                        </p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Information Grid -->
        <div class="grid-2">
            <!-- Personal Information -->
            <div class="profile-card">
                <h3 class="section-title">Personal Information</h3>
                
                <div class="profile-info-row">
                    <span class="profile-label">First Name</span>
                    <span class="profile-value">{{ auth()->user()->first_name ?? 'Not provided' }}</span>
                </div>
                
                <div class="profile-info-row">
                    <span class="profile-label">Last Name</span>
                    <span class="profile-value">{{ auth()->user()->last_name ?? 'Not provided' }}</span>
                </div>
                
                <div class="profile-info-row">
                    <span class="profile-label">Full Name</span>
                    <span class="profile-value">{{ auth()->user()->name ?? 'Not provided' }}</span>
                </div>
                
                <div class="profile-info-row">
                    <span class="profile-label">Email Address</span>
                    <span class="profile-value">{{ auth()->user()->email }}</span>
                </div>
                
                <div class="profile-info-row">
                    <span class="profile-label">Phone Number</span>
                    <span class="profile-value">{{ auth()->user()->phone ?? 'Not provided' }}</span>
                </div>
            </div>

            <!-- Account Information -->
            <div class="profile-card">
                <h3 class="section-title">Account Information</h3>
                
                <div class="profile-info-row">
                    <span class="profile-label">Member Since</span>
                    <span class="profile-value">{{ auth()->user()->created_at->format('F j, Y') }}</span>
                </div>
                
                <div class="profile-info-row">
                    <span class="profile-label">Last Updated</span>
                    <span class="profile-value">{{ auth()->user()->updated_at->format('F j, Y \a\t g:i A') }}</span>
                </div>
                
                <div class="profile-info-row">
                    <span class="profile-label">Email Verified</span>
                    <span class="profile-value">
                        @if(auth()->user()->email_verified_at)
                            <span class="status-badge status-verified">✓ Verified</span>
                        @else
                            <span class="status-badge status-warning">⚠ Not Verified</span>
                        @endif
                    </span>
                </div>
                
                <div class="profile-info-row">
                    <span class="profile-label">User ID</span>
                    <span class="profile-value" style="font-family: monospace;">#{{ auth()->user()->id }}</span>
                </div>
                
                <div class="profile-info-row">
                    <span class="profile-label">Profile Picture</span>
                    <span class="profile-value">
                        @if(auth()->user()->profile_picture)
                            <span class="status-badge status-verified">✓ Uploaded</span>
                        @else
                            <span class="status-badge status-neutral">⚪ Not uploaded</span>
                        @endif
                    </span>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>
