resource "aws_elasticache_subnet_group" "lab_redis_subnet_group" {
  name = "lab-cache"
  subnet_ids = [
    aws_subnet.lab_private_subnet_a.id,
    aws_subnet.lab_private_subnet_b.id,
  ]
}

resource "aws_elasticache_replication_group" "lab_redis" {
  replication_group_id = "lab-valkey-cluster"
  description          = "Valkey Cluster"

  apply_immediately = true
  multi_az_enabled  = false

  parameter_group_name = "default.valkey8"
  engine               = "valkey"
  engine_version       = "8.0"
  node_type            = "cache.t3.micro"
  port                 = 6379

  security_group_ids = [
    aws_security_group.lab_cache_security_group.id
  ]

  subnet_group_name = aws_elasticache_subnet_group.lab_redis_subnet_group.id

  tags = {
    Name = "ValkeyCache"
  }
}
