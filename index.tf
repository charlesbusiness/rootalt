provider "aws" {
  region = "us-east-1"
}


resource "aws_iam_policy" "allow_s3_permissions" {
  name        = "AllowS3Permissions"
  description = "Allow permissions to manage S3 bucket and objects"
  policy      = jsonencode({
    Version = "2012-10-17",
    Statement = [
      {
        Effect   = "Allow"
        Action   = [
          "s3:PutBucketPolicy",
          "s3:PutObject",
          "s3:GetObject"
        ]
        Resource = [
          "arn:aws:s3:::linkcorp-uploads",
          "arn:aws:s3:::linkcorp-uploads/*"
        ]
      }
    ]
  })
}

resource "aws_iam_user_policy_attachment" "attach_policy_to_user" {
  user       = "developer"  # Your IAM user name
  policy_arn = aws_iam_policy.allow_s3_permissions.arn
}

# S3 Bucket for uploads
resource "aws_s3_bucket" "linkcorp_bucket_uploads" {
  bucket = "linkcorp-uploads"

  tags = {
    Name        = "PublicBucket"
    Environment = "Development"
  }
}

# Block Public Access settings to allow public access
resource "aws_s3_bucket_public_access_block" "linkcorp_bucket_public_access_block" {
  bucket = aws_s3_bucket.linkcorp_bucket_uploads.id

  block_public_acls       = false
  ignore_public_acls      = false
  block_public_policy     = false
  restrict_public_buckets = false
}

# Bucket Policy for Public Access
resource "aws_s3_bucket_policy" "linkcorp_bucket_uploads_policy" {
  bucket = aws_s3_bucket.linkcorp_bucket_uploads.id

  policy = jsonencode({
    Version = "2012-10-17",
    Statement = [
      {
        Sid       = "PublicReadGetObject",
        Effect    = "Allow",
        Principal = "*",
        Action    = "s3:GetObject",
        "Resource" : ["arn:aws:s3:::linkcorp-uploads/*"]
      }
    ]
  })
}



