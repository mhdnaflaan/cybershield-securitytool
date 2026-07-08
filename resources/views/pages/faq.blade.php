@extends('layouts.app')

@section('title', 'Frequently Asked Questions')

@section('content')
<div class="container mx-auto px-4 py-10 max-w-4xl">
    <div class="bg-white rounded-2xl shadow-lg p-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-2"> Frequently Asked Questions</h1>
        <p class="text-gray-500 mb-8">Find answers to common questions about CyberShield tools.</p>

        
        <div class="mb-8">
            <input type="text" id="faqSearch" placeholder="Search questions..."
                   class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:outline-none">
        </div>

        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
            <button onclick="filterFaqs('all')" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700 transition">
                All Questions
            </button>
            <button onclick="filterFaqs('general')" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm hover:bg-gray-300 transition">
                General
            </button>
            <button onclick="filterFaqs('tools')" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm hover:bg-gray-300 transition">
                Tools
            </button>
            <button onclick="filterFaqs('security')" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm hover:bg-gray-300 transition">
                Security
            </button>
            <button onclick="filterFaqs('account')" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm hover:bg-gray-300 transition">
                Account
            </button>
            <button onclick="filterFaqs('technical')" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm hover:bg-gray-300 transition">
                Technical
            </button>
        </div>

        
        <div class="space-y-4" id="faqContainer">
            
            <div class="faq-item border border-gray-200 rounded-xl overflow-hidden" data-category="general">
                <button onclick="toggleFaq(this)" class="w-full text-left px-6 py-4 bg-gray-50 hover:bg-gray-100 transition flex justify-between items-center">
                    <span class="font-medium text-gray-800">What is CyberShield.lk?</span>;
                    <span class="text-gray-500 text-xl">+</span>
                </button>
                <div class="faq-answer hidden px-6 py-4 bg-white border-t border-gray-100 text-sm text-gray-600">
                    CyberShield is a free, all-in-one security toolkit designed to help Sri Lankan internet users
                    protect themselves from cyber threats. It offers tools like URL checking, password analysis,
                    SSL verification, hash generation, and more.
                </div>
            </div>

            
            <div class="faq-item border border-gray-200 rounded-xl overflow-hidden" data-category="general">
                <button onclick="toggleFaq(this)" class="w-full text-left px-6 py-4 bg-gray-50 hover:bg-gray-100 transition flex justify-between items-center">
                    <span class="font-medium text-gray-800">Is CyberShield free to use?</span>
                    <span class="text-gray-500 text-xl">+</span>
                </button>
                <div class="faq-answer hidden px-6 py-4 bg-white border-t border-gray-100 text-sm text-gray-600">
                    Yes! CyberShield is completely free for all users. We believe everyone deserves access to
                    security tools without paying for expensive subscriptions.
                </div>
            </div>

            <!-- FAQ Item 3 -->
            <div class="faq-item border border-gray-200 rounded-xl overflow-hidden" data-category="security">
                <button onclick="toggleFaq(this)" class="w-full text-left px-6 py-4 bg-gray-50 hover:bg-gray-100 transition flex justify-between items-center">
                    <span class="font-medium text-gray-800">How does the Password Analyzer work?</span>
                    <span class="text-gray-500 text-xl">+</span>
                </button>
                <div class="faq-answer hidden px-6 py-4 bg-white border-t border-gray-100 text-sm text-gray-600">
                    The Password Analyzer checks your password's strength based on length, character variety, and
                    common patterns. It also checks if your password has appeared in data breaches using the
                    HaveIBeenPwned API.
                </div>
            </div>

            
            <div class="faq-item border border-gray-200 rounded-xl overflow-hidden" data-category="security">
                <button onclick="toggleFaq(this)" class="w-full text-left px-6 py-4 bg-gray-50 hover:bg-gray-100 transition flex justify-between items-center">
                    <span class="font-medium text-gray-800">Is my data stored securely?</span>
                    <span class="text-gray-500 text-xl">+</span>
                </button>
                <div class="faq-answer hidden px-6 py-4 bg-white border-t border-gray-100 text-sm text-gray-600">
                    Yes! All sensitive data is encrypted. Passwords are hashed using bcrypt, and your scan history
                    is stored securely. We never share your data with third parties without your consent.
                </div>
            </div>

            
            <div class="faq-item border border-gray-200 rounded-xl overflow-hidden" data-category="tools">
                <button onclick="toggleFaq(this)" class="w-full text-left px-6 py-4 bg-gray-50 hover:bg-gray-100 transition flex justify-between items-center">
                    <span class="font-medium text-gray-800">What is the URL Safety Checker?</span>
                    <span class="text-gray-500 text-xl">+</span>
                </button>
                <div class="faq-answer hidden px-6 py-4 bg-white border-t border-gray-100 text-sm text-gray-600">
                    The URL Safety Checker uses VirusTotal and Google Safe Browsing to detect phishing, malware,
                    and malicious websites. It helps you identify dangerous links before you click them.
                </div>
            </div>

            
            <div class="faq-item border border-gray-200 rounded-xl overflow-hidden" data-category="tools">
                <button onclick="toggleFaq(this)" class="w-full text-left px-6 py-4 bg-gray-50 hover:bg-gray-100 transition flex justify-between items-center">
                    <span class="font-medium text-gray-800">What is the SSL Checker tool?</span>
                    <span class="text-gray-500 text-xl">+</span>
                </button>
                <div class="faq-answer hidden px-6 py-4 bg-white border-t border-gray-100 text-sm text-gray-600">
                    The SSL Checker verifies a website's SSL certificate and security headers. It gives a grade
                    from A+ to F and provides actionable recommendations to improve website security.
                </div>
            </div>

           
            <div class="faq-item border border-gray-200 rounded-xl overflow-hidden" data-category="account">
                <button onclick="toggleFaq(this)" class="w-full text-left px-6 py-4 bg-gray-50 hover:bg-gray-100 transition flex justify-between items-center">
                    <span class="font-medium text-gray-800">Do I need to create an account?</span>
                    <span class="text-gray-500 text-xl">+</span>
                </button>
                <div class="faq-answer hidden px-6 py-4 bg-white border-t border-gray-100 text-sm text-gray-600">
                    You can use some tools without an account, but creating an account allows you to save your
                    scan history, export PDF reports, and access all features of the platform.
                </div>
            </div>

            
            <div class="faq-item border border-gray-200 rounded-xl overflow-hidden" data-category="technical">
                <button onclick="toggleFaq(this)" class="w-full text-left px-6 py-4 bg-gray-50 hover:bg-gray-100 transition flex justify-between items-center">
                    <span class="font-medium text-gray-800">What technologies are used?</span>
                    <span class="text-gray-500 text-xl">+</span>
                </button>
                <div class="faq-answer hidden px-6 py-4 bg-white border-t border-gray-100 text-sm text-gray-600">
                    CyberShield is built with Laravel (PHP), Tailwind CSS, and MySQL. It integrates with
                    industry-standard APIs like VirusTotal, Google Safe Browsing, and AbuseIPDB.
                </div>
            </div>

            
            <div class="faq-item border border-gray-200 rounded-xl overflow-hidden" data-category="security">
                <button onclick="toggleFaq(this)" class="w-full text-left px-6 py-4 bg-gray-50 hover:bg-gray-100 transition flex justify-between items-center">
                    <span class="font-medium text-gray-800">What is IP Reputation?</span>
                    <span class="text-gray-500 text-xl">+</span>
                </button>
                <div class="faq-answer hidden px-6 py-4 bg-white border-t border-gray-100 text-sm text-gray-600">
                    IP Reputation indicates how likely an IP address is to be malicious. It's based on reports
                    from security researchers and automated systems. The IP Reputation Checker uses AbuseIPDB
                    to provide this information.
                </div>
            </div>

            
            <div class="faq-item border border-gray-200 rounded-xl overflow-hidden" data-category="technical">
                <button onclick="toggleFaq(this)" class="w-full text-left px-6 py-4 bg-gray-50 hover:bg-gray-100 transition flex justify-between items-center">
                    <span class="font-medium text-gray-800">How do I download PDF reports?</span>
                    <span class="text-gray-500 text-xl">+</span>
                </button>
                <div class="faq-answer hidden px-6 py-4 bg-white border-t border-gray-100 text-sm text-gray-600">
                    You can download PDF reports from your dashboard or profile page. Each scan has a "Download PDF"
                    button, and you can also download all reports at once using the "Download All Reports" button.
                </div>
            </div>
        </div>

        
        <div class="mt-8 p-6 bg-blue-50 rounded-xl border border-blue-200 text-center">
            <p class="text-blue-700 text-sm">
                <strong> Still have questions?</strong>
            </p>
            <p class="text-blue-600 text-sm mt-1">
                Contact us at <a href="mailto:support@cybershield.lk" class="hover:underline">support@cybershield</a>
                or use the feedback form below.
            </p>
        </div>
    </div>
</div>

<script>
    function toggleFaq(button) {
        const answer = button.nextElementSibling;
        const icon = button.querySelector('.text-gray-500');
       
        if (answer.classList.contains('hidden')) {
            answer.classList.remove('hidden');
            icon.textContent = '−';
            button.classList.add('bg-blue-50');
        } else {
            answer.classList.add('hidden');
            icon.textContent = '+';
            button.classList.remove('bg-blue-50');
        }
    }

    function filterFaqs(category) {
        const items = document.querySelectorAll('.faq-item');
        const buttons = document.querySelectorAll('.grid button');
       
        
        buttons.forEach(btn => {
            btn.className = 'bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm hover:bg-gray-300 transition';
        });
       
        
        document.querySelectorAll('.grid button').forEach(btn => {
            if (btn.textContent.toLowerCase().includes(category) ||
                (category === 'all' && btn.textContent.includes('All'))) {
                btn.className = 'bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700 transition';
            }
        });
       

        items.forEach(item => {
            if (category === 'all') {
                item.style.display = 'block';
            } else if (item.dataset.category === category) {
                item.style.display = 'block';
            } else {
                item.style.display = 'none';
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('faqSearch');
        searchInput.addEventListener('input', function() {
            const query = this.value.toLowerCase();
            const items = document.querySelectorAll('.faq-item');
           
            items.forEach(item => {
                const question = item.querySelector('button span:first-child').textContent.toLowerCase();
                const answer = item.querySelector('.faq-answer').textContent.toLowerCase();
               
                if (question.includes(query) || answer.includes(query)) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    });
</script>
@endsection