# Week 7 | Bug Bounty and Hardening

## Part 0: Introduction to Hardening
Hardening is a fundamental cybersecurity process focused on strengthening the security of systems, networks, and applications by reducing vulnerabilities and potential attack surfaces. This process involves implementing a set of security measures, guidelines, and best practices designed to protect systems against threats and unauthorized access. By eliminating unnecessary services, applying security patches, configuring security settings, and enforcing strict access controls, hardening helps to minimize the risk of exploitation from both external and internal threats. In the context of cybersecurity, hardening is crucial because it not only enhances the overall security posture of an organization but also helps in compliance with regulatory requirements, protecting sensitive data, and maintaining the integrity and availability of services. As cyber threats continue to evolve in complexity and sophistication, hardening remains an essential proactive defense strategy in the cybersecurity arsenal, ensuring systems are less vulnerable to attacks and breaches.

## Part 1: Common Weakness Enumeration

Before we start searching for security flaws, bugs and other issues broadly known as weaknesses it would be useful to know the industry standard names for the weaknesses we find. The Common Weakness Enumeration (CWE) is a list of common software and hardware weakness types that have security implications. This list is developed by the MITRE corporation and allows for universal language to describe hardware and software vulnerabilities. 

We will cover this more in class later today, and you can learn more about CWEs [here](https://cwe.mitre.org/). If you do identify a weakness to include in your report, do you best to find the industry standard CWE to include in your report.

## Part 2: Docker Dataflow

As we learned from our Threat Modeling exercise a dataflow diagram (DFD) is an essential part of identifying threats and vulnerabilities within a system. Therefore, in order to identify weaknesses and vulnerabilities in our password manager we have provided you this DFD for our password manager!

![Password Manager Dataflow](/lab-writeup-imgs/pw_manager_diagram.png)

## Part 3: Bug Bounty!
Bug Bounties are a big part of cybersecurity. In a Bug Bounty, a company will allow for white hat hackers to attack a designated part of their system to allow for the identification of vulnerabilities before a black hat finds them. It is important that the company specifies the scope of what is permitted to be attacked so that their service remains available for clients and their client's sensitive data remains confidential.

For this lab we will be conducting a bug bounty against our password manager! You will need to look through the password manager and identify vulnerabilities, as well as provide suggestions to remediate the vulnerability.

On canvas under discussions, you will see the [bug reporting write up template](https://canvas.uw.edu/courses/1697011/discussion_topics/8723997). Use this template when you identify a vulnerability and submit it as a response to the discussion board.

Depending on the severity level of the vulnerability you identify, you can receive up to 2 extra credit points to be counted towards your final exam :D

**Note: If another group has already submitted their write up for the bug, you cannot also get credit for finding that vulnerability. This means that the Bug Bounty is effectively a race between all groups to identify all bugs.**

On Saturday night, we will release a lit of all bugs that should have been identified, you then must remediate these bugs to harden our system and protect the password manager from future attacks. If you are unable to find any vulnerabilities before Saturday night, you can still receive full credit for the lab, however you will not be able to get extra credit.

## For Credit:
As always, use the [lab writeup template](/lab-writeup-imgs/info310-lab-template.docx) to complete this assignment. In your steps to reproduce, outline the steps you took to remediate 3 vulnerabilities in your password manager identified in the bug bounty. These can be ones your group identified, ones that were posted by other groups in the discussion board, or ones that were listed in the release on Saturday. 

Be sure to include evidence that these vulnerabilities are patched. This can be screenshots of code, output of security tools or video walk throughs.