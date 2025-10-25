<template>
  <TheWelcome />

  <section class="relative py-20 bg-white dark:bg-gray-900">
    <div class="max-w-7xl mx-auto px-6 lg:px-8">
      <!-- HEADER -->
      <div class="text-center mb-16">
        <p class="text-sm font-semibold text-orange-600 uppercase tracking-wide mb-2">
          Your Privacy Matters
        </p>
        <h1 class="text-4xl md:text-5xl font-extrabold text-gray-900 dark:text-white leading-tight">
          Privacy Policy
        </h1>
        <p class="mt-4 text-lg text-gray-600 dark:text-gray-300 max-w-2xl mx-auto">
          Please read our Privacy Policy carefully to understand how we collect,
          use, and protect your personal information while using
          CareerShyne’s platform.
        </p>
        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400 italic">
          Effective Date: October 2025
        </p>
      </div>

      <!-- MAIN CONTENT -->
      <div class="mx-auto w-full md:w-3/4 lg:w-3/4 text-gray-800 dark:text-gray-300 prose prose-xl max-w-none">
        <h2>1. Introduction</h2>
        <p>
          CareerShyne (“CareerShyne”, “we”, “our”, or “us”) provides
          professional CV creation, job application, and AI-assisted career
          support services through our website
          <a href="https://careershyne.com" class="text-orange-600 hover:underline">careershyne.com</a>. This Privacy
          Policy explains how we collect, use, disclose, and
          safeguard your information when you use our website and related
          services.
        </p>

        <h2>2. Information We Collect</h2>
        <ul>
          <li>
            <strong>Personal Information:</strong> Your name, email address,
            phone number, and CV/resume content provided during profile setup or
            service requests.
          </li>
          <li>
            <strong>Employment Data:</strong> Job preferences, experience,
            education history, and documents you upload when using our services.
          </li>
          <li>
            <strong>Payment Data:</strong> Payments processed through
            <strong>M-PESA (Safaricom)</strong>. We do not store payment
            credentials.
          </li>
          <li>
            <strong>Account & Authentication:</strong> When you use
            <strong>Gmail (Google Authentication)</strong> to sign in or apply
            for jobs, we may collect your basic Google profile information for
            account creation and communication.
          </li>
          <li>
            <strong>Analytics Data:</strong> IP address, browser type, device
            details, and activity logs to help us improve performance and
            usability.
          </li>
        </ul>

        <h2>3. How We Use Your Information</h2>
        <ul>
          <li>To provide, maintain, and enhance our platform and services.</li>
          <li>
            To assist in AI-powered tasks such as CV writing, cover letter
            drafting, or email preparation.
          </li>
          <li>
            To forward applications or career materials to recruiters or
            employers with your consent.
          </li>
          <li>
            To process payments, verify transactions, and prevent fraud or
            unauthorized activity.
          </li>
          <li>To communicate updates, offers, and service-related messages.</li>
        </ul>

        <h2>4. Sharing of Information</h2>
        <p>
          We do not sell your personal information. We only share it with trusted
          third parties where necessary:
        </p>
        <ul>
          <li>Payment processors such as M-PESA for secure transactions.</li>
          <li>
            Recruiters or employers to whom you have authorized us to submit your
            application.
          </li>
          <li>
            Service providers supporting analytics, hosting, and communication
            infrastructure.
          </li>
        </ul>

        <h2>5. Data Security</h2>
        <p>
          We use administrative, technical, and physical safeguards to protect
          your personal information from unauthorized access, alteration, or
          disclosure. However, no system is completely secure, and you use our
          platform at your own risk.
        </p>

        <h2>6. Data Retention</h2>
        <p>
          We retain personal data only as long as necessary to fulfill the
          purposes outlined in this Policy, or as required by applicable law. You
          may request deletion of your data by contacting us directly.
        </p>

        <h2>7. Your Rights</h2>
        <ul>
          <li>Access, update, or delete your personal data.</li>
          <li>Withdraw consent for data processing at any time.</li>
          <li>Request clarification on how your information is handled.</li>
        </ul>

        <h2>8. Cookies & Tracking</h2>
        <p>
          Our site uses cookies to enhance your browsing experience and analyze
          site traffic. You can adjust cookie settings in your browser to limit
          data collection.
        </p>

        <h2>9. Updates to This Policy</h2>
        <p>
          CareerShyne may update this Privacy Policy periodically. The “Effective
          Date” above indicates the latest revision. Continued use of our
          platform after any changes constitutes acceptance of the updated Policy.
        </p>

        <h2>10. Contact Us</h2>
        <p>
          For questions, feedback, or privacy-related requests, contact us via
          our website or email at
          <a href="mailto:support@careershyne.com" class="text-orange-600 hover:underline">support@careershyne.com</a>.
        </p>
      </div>
    </div>
  </section>

  <FooterSection />
</template>

<script setup>
import TheWelcome from "@/components/TheWelcome.vue";
import FooterSection from "@/components/FooterSection.vue";
</script>

<style scoped>
p,
li {
  line-height: 1.75;
}

.prose h2 {
  @apply text-2xl font-semibold text-gray-900 dark:text-white mt-10;
}

.prose a {
  @apply text-blue-500 hover:underline;
}
</style>
