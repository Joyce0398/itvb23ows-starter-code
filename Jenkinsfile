pipeline {
    agent any
        stage('SonarQube') {
            steps {
                script {
                    scannerHome = tool 'SonarQubeScanner'
                    withSonarQubeEnv() {
                        sh "${scannerHome}/bin/sonar-scanner"
                    }
                }
            }
        }
        stage('Unit Tests') {
            agent {
                docker {
                    image 'php:8.2-cli'
                }
            }
            steps {
                unstash 'vendor'
                sh 'vendor/bin/phpunit'
                xunit(
                    thresholds: [
                        failed(failureThreshold: '0'),
                        skipped(unstableThreshold: '0')
                    ],
                    tools: [
                        PHPUnit(
                            pattern: 'build/logs/junit.xml',
                            stopProcessingIfError: true,
                            failIfNotNew: true
                        )
                    ]
                )
            }
        }
    }
}